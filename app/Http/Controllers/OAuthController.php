<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\VcsProviders\StoreVcsProviderAction;
use App\DataTransferObjects\VcsProviderData;
use App\Facades\Auth;
use App\Http\Exceptions\OAuth\ApplicationSuspendedException;
use App\Http\Exceptions\OAuth\RedirectUriMismatchException;
use App\Http\Exceptions\OAuth\OauthException;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as OauthUser;

class OAuthController extends AbstractController
{
    /*
     * TODO: IMPORTANT! Cover this with tests.
     */

    public function __construct(
        private CreateNewUser $createNewUser,
        private StoreVcsProviderAction $storeVcsProvider,
    ) {}

    /**
     * Redirect the user to an external authentication page of an OAuth provider.
     */
    public function login(string $oauthProvider): SymfonyRedirect
    {
        return Socialite::driver($oauthProvider)->redirect();
    }

    /**
     * Handle a callback from an OAuth provider.
     */
    public function callback(string $oauthProvider): RedirectResponse
    {
        // TODO: IMPORTANT! Should I somehow handle accepting terms when registering using OAuth?
        //       Like, show a separate dialog after registration with accepting terms?

        /*
         * TODO: CRITICAL! If I want to access user's repositories I will need specific OAuth "scopes" for it:
         *       https://docs.github.com/en/developers/apps/building-oauth-apps/scopes-for-oauth-apps
         *       https://laravel.com/docs/8.x/socialite
         *       I think I should just add scopes in the first auth redirect here,
         *       but also check that we have the necessary scopes while performing specific actions
         *       and request additional permissions if we don't have it.
         */

        /*
         * TODO: Also handle user avatars provided by OAuth providers. I mean, implement user avatars in general.
         *       Don't store any avatars at all - use an OAuth provided avatar (by its URL)
         *       or use an external service to generate an avatar when OAuth gives nothing or isn't even used.
         */

        $oauthUser = Socialite::driver($oauthProvider)->user();

        $user = $this->findUserByOauthId($oauthProvider, $oauthUser);

        if (is_null($user))
            $user = $this->findUserByEmail($oauthProvider, $oauthUser);

        if (is_null($user))
            $user = $this->registerUserByOauth($oauthProvider, $oauthUser);

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

    /**
     * Try to find a user previously registered via OAuth by their OAuth ID returned from an OAuth provider.
     */
    private function findUserByOauthId(string $oauthProvider, OauthUser $oauthUser): User|null
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('oauth_provider', $oauthProvider)
            ->where('oauth_id', $oauthUser->getId())
            ->first();

        return $user;
    }

    /**
     * Try to find a user by an email returned from an OAuth provider.
     */
    private function findUserByEmail(string $oauthProvider, OauthUser $oauthUser): User|null
    {
        return DB::transaction(function () use ($oauthProvider, $oauthUser) {
            /** @var User|null $user */
            $user = User::query()
                ->where('email', $oauthUser->getEmail())
                ->lockForUpdate()
                ->first();

            if (! is_null($user)) {
                $user->oauthProvider = $oauthProvider;
                $user->oauthId = $oauthUser->getId();

                if (is_null($user->emailVerifiedAt))
                    $user->emailVerifiedAt = now();

                $user->save();
            }

            return $user;
        }, 5);
    }

    /**
     * Register a new user using data returned from an OAuth provider.
     */
    private function registerUserByOauth(string $oauthProvider, OauthUser $oauthUser): User
    {
        /*
         * TODO: IMPORTANT! Don't forget to figure out the rest of the stuff I may want to do here,
         *       like greet the user, send an email or whatever. It should be DRY.
         */

        return DB::transaction(function () use ($oauthUser, $oauthProvider) {
            $user = $this->createNewUser->create([
                'email' => $oauthUser->getEmail(),
                'oauth_provider' => $oauthProvider,
                'oauth_id' => (string) $oauthUser->getId(),
                'terms' => true,
            ]);
            $user->emailVerifiedAt = now();
            $user->save();

            $this->storeVcsProvider->execute(VcsProviderData::fromOauth(
                $oauthUser,
                (string) config("auth.oauth_providers.{$oauthProvider}.vcs_provider"),
                $user,
            ));

            return $user;
        });
    }
}
