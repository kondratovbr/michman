<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

use App\Models\Server;
use Illuminate\Contracts\View\View;
use RuntimeException;

class DeploymentFailedNotification extends AbstractDeploymentNotification
{
    protected bool $broadcast = true;
    
    public static function message(array $data = []): string
    {
        $deployment = static::deployment($data);
        $type = get_called_class();

        return __("notifications.messages.{$type}", [
            'project' => $deployment->project->projectName,
        ]);
    }

    /**
     * Get the name of the view that should be used to display the details of this notification.
     */
    public static function view(array $data = []): View
    {
        $deployment = static::deployment($data);

        /** @var Server $server */
        $server = $deployment->servers()->wherePivot('successful', false)->first();

        if (is_null($server))
            throw new RuntimeException('DeploymentFailedNotification exists but no server with a failed deployment found for the attached deployment.');

        return view('notifications.deployment-failed', [
            'server' => $server,
            'logs' => $server->serverDeployment->logs(),
        ]);
    }
}
