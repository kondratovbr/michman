<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\Actions\VcsProviders\UpdateVcsProviderAction;
use App\DataTransferObjects\VcsProviderDto;
use App\Facades\Auth;
use App\Http\Exceptions\OAuth\ApplicationSuspendedException;
use App\Http\Exceptions\OAuth\RedirectUriMismatchException;
use App\Http\Exceptions\OAuth\OauthException;
use App\Models\User;
use App\Models\OAuthUser as OauthModel;
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

class OAuthController extends AbstractController
{
    public function __construct(
        private CreateNewUser $createNewUser,
        private StoreVcsProviderAction $storeVcsProvider,
        private UpdateVcsProviderAction $updateVcsProvider,
    ) {}

    /** Redirect the user to an external authentication page of an OAuth provider. */
    public function login(string $oauthProvider): SymfonyRedirect
    {
        $socialite = Socialite::driver($oauthProvider);

        $vcsProviderName = $this->getVcsProviderName($oauthProvider);

        if (! is_null($vcsProviderName))
            $socialite->scopes(config("vcs.list.{$vcsProviderName}.oauth_scopes"));

        return $socialite->redirect();
    }

    /** Handle a callback from an OAuth provider. */
    public function callback(string $oauthProvider): RedirectResponse
    {
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

        $oauthUser = Socialite::driver($oauthProvider)->user();

        // Check if user previously registered via OAuth.
        $user = $this->findUserByOauthId($oauthProvider, $oauthUser);

        // Check if user registered normally but tries to log in via OAuth with the same email.
        if (is_null($user)) {
            $user = $this->findUserByEmail($oauthUser);

            /*
             * In this case the user was previously registered with via email:password or a different OAuth provider
             * and now tries to log in via oauth with the same email,
             * so we will tell them to login normally and link accounts in settings.
             * TODO: CRITICAL! Implement that linking. It's currently only works for VCS providers.
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

        $this->updateVcs($oauthProvider, $oauthUser, $user);

        Auth::login($user, true);

        return redirect()->intended(config('fortify.home'));
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
            default => new OauthException($oauthProvider, $request->fullUrl())
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

        return $this->createNewUser->create([
            'email' => $oauthUser->getEmail(),
            'oauth_provider' => $oauthProvider,
            'oauth_id' => (string) $oauthUser->getId(),
            'terms' => true,
        ]);
    }

    /** Create or update a VCS provider for the authenticated user corresponding with the OAuth provider used. */
    private function updateVcs(string $oauthProvider, OauthUser $oauthUser, User $user): void
    {
        DB::transaction(function () use ($oauthProvider, $oauthUser, $user) {
            $vcsProviderName = $this->getVcsProviderName($oauthProvider);

            // This OAuth provider is not configured to be used as a VCS provider.
            if (is_null($vcsProviderName))
                return;

            $vcsProviderData = VcsProviderDto::fromOauth(
                $oauthUser,
                $vcsProviderName,
            );

            $vcsProvider = $user->vcs($vcsProviderName, true);

            // The user has no VcsProvider configured, so we can create one immediately.
            if (is_null($vcsProvider)) {
                $this->storeVcsProvider->execute($vcsProviderData, $user);
                return;
            }

            // If the user has a VcsProvider configured we will update the token,
            // but only if it is associated with the same account,
            // otherwise don't touch it.
            if ($vcsProvider->externalId === $vcsProviderData->external_id)
                $this->updateVcsProvider->execute($vcsProvider,$vcsProviderData);
        }, 5);
    }

    /** Get a VCS provider name from config by its OAuth provider name. */
    private function getVcsProviderName(string $vcsProviderOauthName): string|null
    {
        return config("auth.oauth_providers.{$vcsProviderOauthName}.vcs_provider");
    }
}
