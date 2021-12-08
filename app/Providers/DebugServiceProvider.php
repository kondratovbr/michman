<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DebugServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! isDebug())
            return;

        //
    }
}
