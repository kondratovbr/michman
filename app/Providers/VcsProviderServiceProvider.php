<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VcsProviderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register service classes for VCS provider APIs.
        foreach (config('vcs.list') as $name => $config) {
            if (! empty($config['provider_class']))
                $this->app->bind("{$name}_vcs", $config['provider_class']);
        }
    }
}
