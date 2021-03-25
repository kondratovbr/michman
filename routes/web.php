<?php declare(strict_types=1);

use App\Http\Controllers\DebugController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\TestController;
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

    // TODO: Don't forget to remove this - I don't have a dashboard at all. Maybe later.
    Route::redirect('/dashboard', '/')->name('dashboard');

    Route::resource('servers', ServerController::class)
        ->only(['index', 'show', 'create', 'store']);

});
