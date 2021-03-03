<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Date;
use Carbon\CarbonImmutable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Switch to using the immutable versions of Carbon objects across the whole application.
        Date::use(CarbonImmutable::class);
    }

    public function boot(): void
    {
        //
    }
}
