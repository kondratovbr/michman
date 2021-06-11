<?php declare(strict_types=1);

use App\Http\Controllers\ErrorController;
use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| General Routes
|--------------------------------------------------------------------------
|
| These are general web routes available for everyone.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::redirect('/', '/servers')->name('home');

Route::get('oauth/login/{oauthService}', [OAuthController::class, 'login'])
    ->where('oauthService', implode('|', config('auth.oauth_providers')))
    ->name('oauth.login');
Route::get('oauth/callback/{oauthService}', [OAuthController::class, 'callback'])
    ->where('oauthService', implode('|', config('auth.oauth_providers')))
    ->name('oauth.callback');

/*
 * Error page views to be able to serve those pages directly without exception.
 * Useful for rendering error thrown at the web-server (Nginx) or Livewire levels.
 */
Route::get('404', [ErrorController::class, 'error404'])->name('error.404');
Route::get('401', [ErrorController::class, 'error401'])->name('error.401');
Route::get('403', [ErrorController::class, 'error403'])->name('error.403');
Route::get('413', [ErrorController::class, 'error413'])->name('error.413');
Route::get('419', [ErrorController::class, 'error419'])->name('error.419');
Route::get('429', [ErrorController::class, 'error429'])->name('error.429');
Route::get('500', [ErrorController::class, 'error500'])->name('error.500');
Route::get('503', [ErrorController::class, 'error503'])->name('error.503');
