<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Date;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher;
use App\Services\JobDispatcher;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Switch to using the immutable versions of Carbon objects across the whole application.
        Date::use(CarbonImmutable::class);

        // Use a custom queue dispatcher class.
        $this->app->extend(Dispatcher::class, function ($dispatcher, $app) {
            return new JobDispatcher($app, $dispatcher);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
