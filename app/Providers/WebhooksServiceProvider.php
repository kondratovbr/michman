<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WebhooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register service classes for services that can send us webhooks.
        foreach (config('webhooks.providers') as $hookProvider => $config) {
            if (! empty($config['service_class']))
                $this->app->bind("{$hookProvider}_webhooks", $config['service_class']);
        }
    }
}
