<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServerProviderServiceProvider extends ServiceProvider
{
    /**
     * Register services in the service container (Dependency Injection).
     */
    public function register(): void
    {
        // Register service classes for server provider APIs.
        foreach (config('providers.list') as $name => $config) {
            if (! empty($config['provider_class']))
                $this->app->bindIf($name, $config['provider_class']);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
