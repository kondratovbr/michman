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
use App\Models\VcsProvider;
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

// TODO: IMPORTANT! Should I somehow handle accepting terms when registering using OAuth?
//       Like, show a separate dialog after registration with accepting terms?

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

// TODO: Update tests to account for "is_deleting" flag on users.

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
    public function auth(Request $request, string $oauthProvider): RedirectResponse
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

            $this->vcsProviderHandler->updateViaOAuth($oauthProvider, $oauthUser, $user);

            return redirect()->intended(route('home'));
        }, 5);
    }

    /** Handle an OAuth callback if user is already authenticated and trying to link a new OAuth method. */
    public function link(Request $request, string $oauthProvider): RedirectResponse
    {
        return DB::transaction(function () use ($request, $oauthProvider): RedirectResponse {
            $user = Auth::user()->freshLockForUpdate();

            $oauthModel = $user->oauth($oauthProvider);

            // This provider is already linked to this user, something surely went wrong.
            if (! is_null($oauthModel)) {
                throw new OAuthException(
                    $oauthProvider,
                    $request->fullUrl(),
                    'Tried to link user to an OAuth provider that is already linked.',
                );
            }

            /** @var OauthUser $oauthUser */
            $oauthUser = Socialite::driver($oauthProvider)
                ->redirectUrl(route('oauth.link-callback', $oauthProvider))
                ->user();

            $oauthModel = $this->createOAuthUser->execute(
                $oauthProvider,
                $oauthUser->getId(),
                $oauthUser->getNickname(),
                $user,
            );

            // TODO: Cover this association with a test.
            /** @var VcsProvider|null $vcsProvider */
            if (
                $vcsProvider = $user->vcsProviders()
                    ->where('provider', config("auth.oauth_providers.{$oauthProvider}.vcs_provider"))
                    ->where('external_id', $oauthModel->oauthId)
                    ->latest()
                    ->first()
            ) {
                $vcsProvider->oauthUser()->associate($oauthModel)->save();
            }

            /*
             * TODO: IMPORTANT! This doesn't really work because these flashes use broadcasting,
             *       which happens immediately (if the queue workers aren't too loaded),
             *       but nobody is listening at the moment (the user's page is being reloaded after a redirect).
             *       Other flashes in this OAuth logic may not work as well. Need to figure out a different way
             *       to communicate with the user.
             *       Probably make the front-end part for the Laravel's session status flashes and use it here.
             *       Same thing in VcsProviderController.
             */
            flash(__('flash.oauth-linked', [
                'oauth' => __("auth.oauth.providers.{$oauthProvider}.label"),
            ]), FlashMessageEvent::STYLE_SUCCESS);

            return redirect()->route('account.show', 'profile');
        }, 5);
    }

    /** Unlink the current user's account from an OAuth account. */
    public function unlink(string $oauthProvider): RedirectResponse
    {
        DB::transaction(function () use ($oauthProvider) {
            $user = Auth::user()->freshLockForUpdate();

            $oauthModel = $user->oauth($oauthProvider);

            if (is_null($oauthModel))
                return;

            if ($user->oauthUsers()->count() == 1 && ! $user->usesPassword()) {
                flash(__('flash.set-up-password-to-disable-oauth'), FlashMessageEvent::STYLE_WARNING);
                return;
            }

            $oauthModel->purge();

            $vcsProviderName = $this->vcsProviderHandler->getVcsProviderName($oauthProvider);

            if (is_null($vcsProviderName))
                return;

            $vcsProvider = $user->vcs($vcsProviderName);

            if (is_null($vcsProvider)) {
                flash(__('flash.oauth-unlinked', [
                    'oauth' => __("auth.oauth.providers.{$oauthProvider}.label"),
                ]));
            } else {
                flash(__('flash.oauth-unlinked-vcs-kept', [
                    'oauth' => __("auth.oauth.providers.{$oauthProvider}.label"),
                ]));
            }
        }, 5);

        return redirect()->route('account.show', 'profile');
    }

    /** Try to find a user previously registered via OAuth by their OAuth ID returned from an OAuth provider. */
    private function findUserByOauthId(string $oauthProvider, OauthUser $oauthUser): User|null
    {
        /** @var OauthModel|null $oauth */
        $oauth = OauthModel::query()
            ->where('provider', $oauthProvider)
            ->where('oauth_id', $oauthUser->getId())
            ->first();

        if (! $oauth)
            return null;

        if ($oauth->user?->isDeleting)
            return null;

        return $oauth->user;
    }

    /** Try to find a user by an email returned from an OAuth provider. */
    private function findUserByEmail(OauthUser $oauthUser): User|null
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('email', $oauthUser->getEmail())
            ->notDeleting()
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

            $oauthModel = $user->oauth($oauthProvider);

            $vcsProvider = $this->vcsProviderHandler->createViaOAuth($oauthProvider, $oauthUser, $user);

            // TODO: Cover this association with a test.
            $vcsProvider->oauthUser()->associate($oauthModel)->save();

            event(new Registered($user));

            return $user;
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
            'application_suspended' => new ApplicationSuspendedException($oauthProvider, $request->fullUrl()),
            'redirect_uri_mismatch' => new RedirectUriMismatchException($oauthProvider, $request->fullUrl()),
            default => new OAuthException($oauthProvider, $request->fullUrl())
        };
    }
}
