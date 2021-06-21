<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VcsProviderServiceProvider extends ServiceProvider
{
    /**
     * Register services in the service container (Dependency Injection).
     */
    public function register(): void
    {
        // Register service classes for VCS provider APIs.
        foreach (config('vcs.list') as $name => $config) {
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
