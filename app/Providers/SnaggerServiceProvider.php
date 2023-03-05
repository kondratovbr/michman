<?php declare(strict_types=1);

namespace App\Providers;

use App\Services\LogSnag;
use Illuminate\Support\ServiceProvider;

class SnaggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LogSnag::class, function () {
            return new LogSnag(
                token: (string) config('services.logsnag.api_key'),
            );
        });
    }
}
