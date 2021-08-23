<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

use App\Models\Deployment;

class DeploymentFailedNotification extends AbstractDeploymentNotification
{
    protected bool $broadcast = true;

    protected static function getMessage(?Deployment $deployment): string
    {
        $type = get_called_class();

        return __("notifications.messages.{$type}", [
            'project' => $deployment->project->projectName,
        ]);
    }

    /**
     * Get the name of the view that should be used to display the details of this notification.
     */
    public static function view(): string
    {
        return 'notifications.deployment-failed';
    }
}
