<?php declare(strict_types=1);

namespace App\Providers;

use App\Events\Deployments\DeploymentFinishedEvent;
use App\Events\Deployments\DeploymentFailedEvent;
use App\Listeners\DispatchProjectUpdatedEventListener;
use App\Listeners\HandleFinishedDeploymentListener;
use App\Listeners\SendFailedDeploymentNotificationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var string[][] The event listener mappings for the application. */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        DeploymentFinishedEvent::class => [
            HandleFinishedDeploymentListener::class,
            DispatchProjectUpdatedEventListener::class,
        ],

        DeploymentFailedEvent::class => [
            SendFailedDeploymentNotificationListener::class,
            DispatchProjectUpdatedEventListener::class,
        ],
    ];
}
