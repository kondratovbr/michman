<?php declare(strict_types=1);

use App\Http\Controllers\DebugController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
|
| This routes are loaded only when the application is in debug mode.
| I.e. config app.env === 'debug'.
| Feel free to add anything you need for development.
|
*/

Route::get('test', TestController::class);
Route::get('routes', [DebugController::class, 'routes']);
Route::get('phpinfo', [DebugController::class, 'phpInfo']);
Route::get('empty', [DebugController::class, 'empty']);
Route::get('blank', [DebugController::class, 'blank']);
Route::get('pusher', [DebugController::class, 'pusher']);
