<?php declare(strict_types=1);

namespace App\Providers;

use App\Events\Deployments\DeploymentCompletedEvent;
use App\Events\Deployments\DeploymentFailedEvent;
use App\Listeners\HandleFinishedDeploymentListener;
use App\Listeners\SendFailedDeploymentNotificationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var string[][]
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DeploymentCompletedEvent::class => [
            HandleFinishedDeploymentListener::class,
        ],
        DeploymentFailedEvent::class => [
            SendFailedDeploymentNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
