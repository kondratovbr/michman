<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Livewire\AccountView;
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
    ->where(
        'show',
        implode('|', Arr::keys(AccountView::VIEWS))
    )
    ->name('account.show');

/*
 * Server routes
 */
Route::resource('servers', Controllers\ServerController::class)
    ->only(['index', 'show']);
