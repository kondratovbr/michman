<?php declare(strict_types=1);

use App\Http\Controllers\VcsProviderController;
use App\Http\Controllers\ServerController;
use App\Http\Livewire\AccountView;
use App\Http\Livewire\ServerView;
use App\Support\Arr;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| App Routes
|--------------------------------------------------------------------------
|
| These routes are available only for authenticated users with verified emails
| by the means of Laravel Sanctum.
| Guest are redirected to the login page.
|
*/

/*
 * Custom user account routes
 */
Route::redirect('/account', '/account/profile');
Route::get('/account/{show}', AccountView::class)
    ->where('show', implode('|', Arr::keys(AccountView::VIEWS)))
    ->name('account.show');

/*
 * Servers routes
 */
Route::get('servers', [ServerController::class, 'index'])->name('server.index');
Route::get('servers/{server}/{show}', ServerView::class)
    ->where('show', implode('|', Arr::keys(ServerView::VIEWS)))
    ->name('servers.show');


/*
 * VcsProviders routes
 */
Route::name('vcs.')->group(function () {
    Route::get('vcs/link/{vcsProviderOauthName}', [VcsProviderController::class, 'redirect'])
        ->name('redirect');
    Route::get('oauth/{vcsProviderOauthName}/vcs-callback', [VcsProviderController::class, 'callback'])
        ->name('callback');
    Route::get('vcs/{vcsProviderOauthName}/unlink', [VcsProviderController::class, 'unlink'])
        ->name('unlink');
});
