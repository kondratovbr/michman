<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\Deployments\DeploymentFailedEvent;
use App\Notifications\Deployments\DeploymentFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendFailedDeploymentNotificationListener extends AbstractEventListener implements ShouldQueue
{
    public function handle(DeploymentFailedEvent $event): void
    {
        $event->deployment()->project->user->notify(new DeploymentFailedNotification($event->deployment()));
    }
}
