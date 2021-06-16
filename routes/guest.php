<?php declare(strict_types=1);

use App\Http\Controllers\OAuthController;
use App\Support\Arr;
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
Route::prefix('oauth/{oauthService}')
    ->name('oauth.')
    ->where([
        'oauthService' => implode('|', Arr::keys(config('auth.oauth_providers'))),
    ])
    ->group(function () {
        Route::get('/', [OAuthController::class, 'defaultCallback'])->name('default-callback');
        Route::get('login', [OAuthController::class, 'login'])->name('login');
        Route::get('callback', [OAuthController::class, 'callback'])->name('callback');
    });
