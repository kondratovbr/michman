<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Facades\Auth;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends AbstractController
{
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
    public function callback(string $oauthProvider, CreateNewUser $createNewUser): RedirectResponse
    {
        // TODO: IMPORTANT! Should I somehow handle accepting terms when registering using OAuth? Like, show a separate dialog after registration with accepting terms?

        // TODO: CRITICAL! CONTINUE! Figure out what to do here to actually register (if needed) and authenticate a user. Check out the code in Fortify.
        //       Also, figure out WTF is "scopes":
        //       https://laravel.com/docs/8.x/socialite
        //       https://docs.github.com/en/developers/apps/building-oauth-apps/scopes-for-oauth-apps
        //       Don't forget to implement VCS providers and automatically create one when a user is registered using OAuth.

        /*
         * TODO: CRITICAL! CONTINUE! Go over all the authentication logic and make sure than null password doesn't break anything,
         *       and a user cannot login with an empty password when null is stored in the DB.
         */

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
         * TODO: CRITICAL! I should handle a situation when user is already registered using an email+password
         *       and tries to OAuth which gives us the same email
         *       - should just let the user in the account that uses that email.
         */

        $oauthUser = Socialite::driver($oauthProvider)->user();

        /** @var User|null $user */
        $user = User::query()
            ->where('oauth_provider', $oauthProvider)
            ->where('oauth_id', $oauthUser->getId())
            ->first();

        if (is_null($user)) {
            /*
             * TODO: Also handle user avatars provided by OAuth providers. I mean, implement user avatars in general.
             *       Don't store any avatars at all - use an OAuth provided avatar (by its URL)
             *       or use an external service to generate an avatar when OAuth gives nothing or isn't even used.
             */

            /*
             * TODO: IMPORTANT! Don't forget the rest of the stuff I may want to do here,
             *       like greet the user, send an email or whatever.
             */

            $user = DB::transaction(function () use ($createNewUser, $oauthUser, $oauthProvider) {
                $user = $createNewUser->create([
                    'email' => $oauthUser->getEmail(),
                    'oauth_provider' => $oauthProvider,
                    'oauth_id' => (string) $oauthUser->getId(),
                    'terms' => true,
                ]);
                $user->emailVerifiedAt = now();
                $user->save();
            });
        }

        Auth::login($user, true);

        // TODO: CRITICAL! Try this redirect with an actual button on an actual page (works with manual URLs in the address bar).
        //       That's probably how it's done in Laravel Fortify.
        return redirect()->intended(config('fortify.home'));
    }
}
