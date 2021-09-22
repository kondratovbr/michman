<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Miscellaneous Routes
|--------------------------------------------------------------------------
|
| These are the routes that are neither API, nor web
| and don't belong to any defined group.
| Any necessary middleware and parameters should be defined per-route.
| These routes are loaded by the RouteServiceProvider
| with no common configuration.
|
*/

// Register route for spatie/laravel-webhook-client package to handle incoming webhook requests.
Route::middleware([
    'throttle:webhooks',
    SubstituteBindings::class,
])->group(function () {
    Route::post('hook/{webhookProvider}/{webhook}', WebhookController::class)
        ->name('hook.push');

    // Route::webhooks('hook');
});
