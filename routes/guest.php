<?php declare(strict_types=1);

use App\Http\Controllers\OAuthController;
use App\Support\Arr;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
|
| These routes are available only for guests, i.e. non-authenticated users
| by covering them with "guest" middleware.
| Authenticated users will be redirected to the homepage.
|
*/

Route::get('oauth/login/{oauthService}', [OAuthController::class, 'login'])
    ->where(
        'oauthService',
        implode('|', Arr::keys(config('auth.oauth_providers')))
    )
    ->name('oauth.login');
Route::get('oauth/callback/{oauthService}', [OAuthController::class, 'callback'])
    ->where(
        'oauthService',
        implode('|', Arr::keys(config('auth.oauth_providers')))
    )
    ->name('oauth.callback');
