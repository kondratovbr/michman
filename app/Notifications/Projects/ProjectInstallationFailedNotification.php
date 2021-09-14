<?php declare(strict_types=1);

namespace App\Notifications\Projects;

class ProjectInstallationFailedNotification extends AbstractProjectNotification
{
    /**
     * Get the notification message to show in the UI.
     */
    public static function message(array $data = []): string
    {
        $project = static::project($data);
        $type = get_called_class();

        return __("notifications.messages.{$type}", [
            'project' => $project->projectName,
        ]);
    }
}
