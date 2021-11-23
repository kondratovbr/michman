<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\OAuthUsers\CreateOAuthUserAction;
use App\Events\Users\FlashMessageEvent;
use App\Facades\Auth;
use App\Handlers\VcsProviderHandler;
use App\Http\Exceptions\OAuth\ApplicationSuspendedException;
use App\Http\Exceptions\OAuth\RedirectUriMismatchException;
use App\Http\Exceptions\OAuth\OAuthException;
use App\Models\User;
use App\Models\OAuthUser as OauthModel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as OauthUser;

/*
 * TODO: IMPORTANT! Will have to figure out how to revoke tokens in case of a breach.
 *       Users will probably have to re-authorize, so I'll have to prepare a plan for such scenario.
 */

// TODO: CRITICAL! Update tests to reflect the new logic.

// TODO: IMPORTANT! Should I somehow handle accepting terms when registering using OAuth?
//       Like, show a separate dialog after registration with accepting terms?
// TODO: CRITICAL! Don't forget to write these terms, btw.

/*
 * TODO: IMPORTANT! Figure out what to do if a user changes out permissions in the GitHub UI, for example.
 *       Will have to catch that error and notify the user - ask to re-authorize us immediately,
 *       if it happened in out UI, or send a link in an email otherwise.
 */

/*
 * TODO: IMPORTANT! Also handle user avatars provided by OAuth providers. I mean, implement user avatars in general.
 *       Don't store any avatars at all - use an OAuth provided avatar (by its URL)
 *       or use an external service to generate an avatar when OAuth gives nothing or isn't even used.
 */

class OAuthController extends AbstractController
{
    public function __construct(
        private CreateNewUser $createNewUser,
        private CreateOAuthUserAction $createOAuthUser,
        private VcsProviderHandler $vcsProviderHandler,
    ) {}

    /** Redirect the user to an external authentication page of an OAuth provider for auth purposes. */
    public function redirectAuth(string $oauthProvider): SymfonyRedirect
    {
        $socialite = Socialite::driver($oauthProvider);

        $vcsProviderName = $this->vcsProviderHandler->getVcsProviderName($oauthProvider);

        if (! is_null($vcsProviderName))
            $socialite->scopes(config("vcs.list.{$vcsProviderName}.oauth_scopes"));

        return $socialite
            ->redirectUrl(route('oauth.auth-callback', $oauthProvider))
            ->redirect();
    }

    /** Redirect the user to an external authentication page of an OAuth provider for linking purposes. */
    public function redirectLink(string $oauthProvider): SymfonyRedirect
    {
        $socialite = Socialite::driver($oauthProvider);

        return $socialite
            ->redirectUrl(route('oauth.link-callback', $oauthProvider))
            ->redirect();
    }

    /** Handle an OAuth callback if user is trying to register or login. */
    protected function auth(Request $request, string $oauthProvider): RedirectResponse
    {
        return DB::transaction(function () use ($request, $oauthProvider): RedirectResponse {
            $oauthUser = Socialite::driver($oauthProvider)
                ->redirectUrl(route('oauth.auth-callback', $oauthProvider))
                ->user();

            // Check if user previously registered via OAuth with this provider.
            $user = $this->findUserByOauthId($oauthProvider, $oauthUser);

            if (is_null($user)) {
                $user = $this->findUserByEmail($oauthUser);

                /*
                 * In this case the user was previously registered with via email:password or a different OAuth provider
                 * and now tries to log in via OAuth with the same email,
                 * so we will tell them to login normally and link accounts in settings.
                 */
                if (! is_null($user)) {
                    session()->flash('status', __('flash.oauth-failed-email-taken', [
                        'oauth_provider' => __("auth.oauth.providers.{$oauthProvider}.label"),
                    ]));
                    return redirect()->route('login');
                }
            }

            // If this is a new user trying to register via OAuth - create a new account for them.
            if (is_null($user))
                $user = $this->registerUserViaOauth($oauthProvider, $oauthUser);

            Auth::login($user);

            $this->vcsProviderHandler->update($oauthProvider, $oauthUser, $user);

            return redirect()->intended(route('home'));
        }, 5);
    }

    /** Handle an OAuth callback if user is already authenticated and trying to link a new OAuth method. */
    protected function link(Request $request, string $oauthProvider): RedirectResponse
    {
        return DB::transaction(function () use ($request, $oauthProvider): RedirectResponse {
            $user = Auth::user();

            $oauthModel = $user->oauth($oauthProvider);

            // This provider is already linked to this user, something surely went wrong.
            if (! is_null($oauthModel)) {
                throw new OAuthException(
                    $oauthProvider,
                    $request->fullUrl(),
                    'Tried to link user to an OAuth provider that is already linked.',
                );
            }

            $oauthUser = Socialite::driver($oauthProvider)
                ->redirectUrl(route('oauth.link-callback', $oauthProvider))
                ->user();

            $this->createOAuthUser->execute(
                $oauthProvider,
                $oauthUser->getId(),
                $oauthUser->getNickname(),
                $user,
            );

            flash(__('flash.oauth-linked', [
                'oauth' => __("auth.oauth.providers.{$oauthProvider}.label"),
            ]), FlashMessageEvent::STYLE_SUCCESS);

            return redirect()->route('account.show', 'profile');
        }, 5);
    }

    /** Handle an OAuth callback if user is authenticated and trying to link a VCS provider. */
    protected function vcs(Request $request, string $oauthProvider): RedirectResponse
    {
        return DB::transaction(function () use ($request, $oauthProvider): RedirectResponse {
            $user = Auth::user();

            $oauthUser = Socialite::driver($oauthProvider)
                ->redirectUrl(route('vcs.link-callback', $oauthProvider))
                ->user();

            $vcs = $user->vcs($this->vcsProviderHandler->getVcsProviderName($oauthProvider));

            if (is_null($vcs)) {
                $vcs = $this->vcsProviderHandler->create($oauthProvider, $oauthUser, $user);

                flash(__('flash.vcs-provider-linked', [
                    'vcs' => __("projects.repo.providers.{$vcs->provider}"),
                ]), FlashMessageEvent::STYLE_SUCCESS);
            } else {
                $this->vcsProviderHandler->update($oauthProvider, $oauthUser, $user);

                flash(__('flash.vcs-provider-updated', [
                    'vcs' => __("projects.repo.providers.{$vcs->provider}"),
                ]), FlashMessageEvent::STYLE_SUCCESS);
            }

            return redirect()->route('account.show', 'vcs');
        }, 5);
    }

    /**
     * Handle a callback from an OAuth provider in case of an error during authentication.
     *
     * https://docs.github.com/en/developers/apps/managing-oauth-apps/troubleshooting-authorization-request-errors
     */
    public function defaultCallback(string $oauthProvider, Request $request): RedirectResponse
    {
        $error = $request->get('error');

        // User declined access on the OAuth provider side so we will just redirect to
        // the beginning for now.
        // TODO: Maybe show some message for the user in this case.
        if ($error === 'access_denied')
            return redirect()->home();

        throw match ($error) {
            // TODO: CRITICAL! If any of these OAuthExceptions is thrown I should immediately notify myself on the emergency channel.
            'application_suspended' => new ApplicationSuspendedException($oauthProvider, $request->fullUrl()),
            'redirect_uri_mismatch' => new RedirectUriMismatchException($oauthProvider, $request->fullUrl()),
            default => new OAuthException($oauthProvider, $request->fullUrl())
        };
    }

    /** Try to find a user previously registered via OAuth by their OAuth ID returned from an OAuth provider. */
    private function findUserByOauthId(string $oauthProvider, OauthUser $oauthUser): User|null
    {
        /** @var OauthModel|null $oauth */
        $oauth = OauthModel::query()
            ->where('provider', $oauthProvider)
            ->where('oauth_id', $oauthUser->getId())
            ->first();

        return optional($oauth)->user;
    }

    /** Try to find a user by an email returned from an OAuth provider. */
    private function findUserByEmail(OauthUser $oauthUser): User|null
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('email', $oauthUser->getEmail())
            ->first();

        return $user;
    }

    /** Register a new user using data returned from an OAuth provider. */
    private function registerUserViaOauth(string $oauthProvider, OauthUser $oauthUser): User
    {
        /*
         * TODO: IMPORTANT! Don't forget to figure out the rest of the stuff I may want to do here,
         *       like greet the user, send an email or whatever. It should be DRY.
         */

        return DB::transaction(function () use ($oauthProvider, $oauthUser): User {
            $user = $this->createNewUser->create([
                'email' => $oauthUser->getEmail(),
                'oauth_provider' => $oauthProvider,
                'oauth_id' => (string) $oauthUser->getId(),
                'oauth_nickname' => $oauthUser->getNickname(),
                'terms' => true,
            ]);

            $this->vcsProviderHandler->create($oauthProvider, $oauthUser, $user);

            event(new Registered($user));

            return $user;
        }, 5);
    }
}
