<?php declare(strict_types=1);

namespace App\Providers;

use App\Events\Deployments\DeploymentFinishedEvent;
use App\Events\Deployments\DeploymentFailedEvent;
use App\Listeners\AddSubscriberToMailingService;
use App\Listeners\DispatchProjectUpdatedEventListener;
use App\Listeners\HandleFinishedDeploymentListener;
use App\Listeners\SendFailedDeploymentNotificationListener;
use App\Listeners\StorePurchaseBrowserEventListener;
use App\Listeners\StoreUserRegisteredBrowserEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Paddle\Events\SubscriptionCreated;

class EventServiceProvider extends ServiceProvider
{
    /** @var string[][] The event listener mappings for the application. */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            StoreUserRegisteredBrowserEventListener::class,
        ],

        Verified::class => [
            AddSubscriberToMailingService::class,
        ],

        DeploymentFinishedEvent::class => [
            HandleFinishedDeploymentListener::class,
            DispatchProjectUpdatedEventListener::class,
        ],

        DeploymentFailedEvent::class => [
            SendFailedDeploymentNotificationListener::class,
            DispatchProjectUpdatedEventListener::class,
        ],

        SubscriptionCreated::class => [
            StorePurchaseBrowserEventListener::class,
        ],
    ];

    /** Determine if events and listeners should be automatically discovered. */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
