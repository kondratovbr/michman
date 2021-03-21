<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->registerRoutes();
    }

    /**
     * Register application routes
     */
    protected function registerRoutes(): void
    {
        $this->routes(function () {

            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    $this->registerApiRoutes();
                });

            Route::middleware('web')
                ->group(function () {
                    $this->registerWebRoutes();
                });

        });
    }

    /**
     * Register API routes
     */
    protected function registerApiRoutes(): void
    {
        Route::group([], base_path('routes/api.php'));
    }

    /**
     * Register web routes
     */
    protected function registerWebRoutes(): void
    {
        // Register general routes
        Route::group([], base_path('routes/web.php'));

        // Register guest routes
        Route::middleware('guest')
            ->group(base_path('routes/guest.php'));

        // Register auth'ed user routes
        Route::middleware(['auth:sanctum', 'verified'])
            ->group(base_path('routes/app.php'));

        // Register debug web routes
        // "if" is important here - this way the debug routes
        // won't even be registered in production,
        // including when caching by "artisan route:cache".
        if (isDebug()) {
            Route::middleware('debug')
                ->prefix('debug')
                ->group(base_path('routes/debug.php'));
        }
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
