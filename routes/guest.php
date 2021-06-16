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

Route::get('oauth/{oauthService}/login', [OAuthController::class, 'login'])
    ->where(
        'oauthService',
        implode('|', Arr::keys(config('auth.oauth_providers')))
    )
    ->name('oauth.login');

Route::get('oauth/{oauthService}/callback', [OAuthController::class, 'callback'])
    ->where(
        'oauthService',
        implode('|', Arr::keys(config('auth.oauth_providers')))
    )
    ->name('oauth.callback');
