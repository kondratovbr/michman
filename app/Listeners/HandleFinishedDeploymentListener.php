<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\Deployments\DeploymentFinishedEvent;
use App\Notifications\Deployments\DeploymentCompletedNotification;
use App\Notifications\Deployments\DeploymentFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HandleFinishedDeploymentListener extends AbstractEventListener implements ShouldQueue
{
    public function handle(DeploymentFinishedEvent $event): void
    {
        if ($event->deployment()->successful) {
            $event->deployment()->project->user->notify(new DeploymentCompletedNotification($event->deployment()));
            return;
        }

        if ($event->deployment()->failed) {
            $event->deployment()->project->user->notify(new DeploymentFailedNotification($event->deployment()));
            return;
        }

        Log::error('HandleFinishedDeploymentListener: Deployment finished, but neither successful nor failed.');
    }
}
