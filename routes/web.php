<?php declare(strict_types=1);

use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/servers')->name('home');

/*
 * Guest routes
 */
//



/*
 * Auth'ed user routes
 */
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('servers', ServerController::class)
        ->only(['index', 'show', 'create', 'store']);

});



/*
 * Debug routes (not loaded if not in debug mode)
 */
if (isDebug()) {
    Route::prefix('debug')->group(function () {
        Route::get('routes', fn() => 'Here will be the list of routes!');
    });
}
