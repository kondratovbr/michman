<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Illuminate\Http\Request;
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
    public function callback(string $oauthProvider): void
    {
        // TODO: CRITICAL! CONTINUE! Figure out what to do here to actually register (if needed) and authenticate a user. Check out the code in Fortify.
        //       Also, figure out WTF is scopes:
        //       https://laravel.com/docs/8.x/socialite
        //       https://docs.github.com/en/developers/apps/building-oauth-apps/scopes-for-oauth-apps
        //       Don't forget to implement VCS providers and automatically create one when a user is registered using OAuth.

        /*
         * TODO: Next - refactor login.blade.php and auth-box.blade.php to add another block underneath
         *       with the link to login/register and also add OAuth buttons on the corresponding pages.
         */

        $user = Socialite::driver($oauthProvider)->user();
        
        //
    }
}
