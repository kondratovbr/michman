<?php declare(strict_types=1);

use App\Http\Controllers\BillingDisabledController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\VcsProviderController;
use App\Http\Controllers\ServerController;
use App\Http\Livewire\AccountView;
use App\Http\Livewire\ProjectView;
use App\Http\Livewire\ServerView;
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

// Show a temporary page instead of the billing portal until billing is enabled.
Route::get('/billing-disabled', BillingDisabledController::class)
    ->name('billing-disabled');

/*
 * Custom user account routes
 */
Route::redirect('/account', '/account/profile');
Route::get('/account/{show?}', AccountView::class)
    ->where('show', AccountView::viewsValidationRegex())
    ->name('account.show');

/*
 * Servers routes
 */
Route::get('servers', [ServerController::class, 'index'])->name('servers.index');
Route::get('servers/{server}/{show?}', ServerView::class)
    ->where('show', ServerView::viewsValidationRegex())
    ->name('servers.show');

/*
 * OAuthUsers routes
 */
Route::name('oauth.')
    ->prefix('oauth/{oauthService}')
    ->group(function () {
        Route::get('link', [OAuthController::class, 'redirectLink'])
            ->name('link');
        Route::get('callback/link', [OAuthController::class, 'link'])
            ->name('link-callback');
        Route::get('oauth/unlink', [OAuthController::class, 'unlink'])
            ->name('unlink');
    });

/*
 * VcsProviders routes
 */
Route::name('vcs.')->group(function () {
    Route::get('vcs/{vcsProviderOauthName}/link', [VcsProviderController::class, 'redirect'])
        ->name('link');
    Route::get('oauth/{vcsProviderOauthName}/callback/vcs', [VcsProviderController::class, 'link'])
        ->name('link-callback');
    Route::get('vcs/{vcsProviderOauthName}/unlink', [VcsProviderController::class, 'unlink'])
        ->name('unlink');
});

/*
 * Projects routes
 */
Route::get('projects/{project}/{show?}', ProjectView::class)
    ->where('show', ProjectView::viewsValidationRegex())
    ->name('projects.show');
