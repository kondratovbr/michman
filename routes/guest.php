<?php declare(strict_types=1);

use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
|
| These routes are available only for guests, i.e. non-authenticated users.
| Which is achieved by covering them with "guest" middleware in RouteServiceProvider.
| Authenticated users will be redirected to the homepage.
|
*/

/*
 * OAuth routes
 */
Route::name('oauth.')
    ->prefix('oauth/{oauthService}')
    ->group(function () {
        Route::get('auth', [OAuthController::class, 'redirectAuth'])
            ->name('auth');
        Route::get('callback/auth', [OAuthController::class, 'auth'])
            ->name('auth-callback');
    });
