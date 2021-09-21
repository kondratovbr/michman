<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServerProviderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register service classes for server provider APIs.
        foreach (config('providers.list') as $name => $config) {
            if (! empty($config['provider_class']))
                $this->app->bind("{$name}-servers", $config['provider_class']);
        }
    }
}
