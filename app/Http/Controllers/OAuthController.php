<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Facades\Auth;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

        // TODO: IMPORTANT! Figure out WTF is "scopes":
        //       https://laravel.com/docs/8.x/socialite
        //       https://docs.github.com/en/developers/apps/building-oauth-apps/scopes-for-oauth-apps
        //       Don't forget to implement VCS providers and automatically create one when a user is registered using OAuth.

        /*
         * TODO: IMPORTANT! Make sure to handle a case when a user declines access on the OAuth provider side for some reason, see:
         *       https://docs.github.com/en/developers/apps/managing-oauth-apps/troubleshooting-authorization-request-errors
         *       Maybe other possible errors as well, for example - "Application suspended" error should be handled like an
         *       emergency situation and I should be notified immediately.
         */

        /*
         * TODO: Next - refactor login.blade.php and auth-box.blade.php to add another block underneath
         *       with the link to login/register and also add OAuth buttons on the corresponding pages.
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

        // TODO: CRITICAL! Try this redirect with an actual button on an actual page (works with manual URLs in the address bar).
        //       That's probably how it's done in Laravel Fortify.
        return redirect()->intended(config('fortify.home'));
    }

    /**
     * Try to authenticate a user by OAuth ID returned from an OAuth provider.
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
     * Try to authenticate a user by an Email returned from OAuth provider.
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
     * Register a new user using data returned from OAuth provider.
     */
    private function registerUserByOauth(string $oauthProvider, OauthUser $oauthUser): User
    {
        /*
         * TODO: IMPORTANT! Don't forget to figure out the rest of the stuff I may want to do here,
         *       like greet the user, send an email or whatever.
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

            return $user;
        });
    }
}
