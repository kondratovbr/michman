<?php declare(strict_types=1);

use App\Http\Controllers;
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

Route::resource('servers', Controllers\ServerController::class)
    ->only(['index', 'show']);
