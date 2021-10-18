<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

use App\Models\Server;
use App\Models\User;
use App\Notifications\Interfaces\Viewable;
use Illuminate\Contracts\View\View;
use Illuminate\Notifications\Messages\MailMessage;
use RuntimeException;

class DeploymentFailedNotification extends AbstractDeploymentNotification implements Viewable
{
    protected bool $mail = true;

    /** Get the mail representation of the notification. */
    public function toMail(User $notifiable): MailMessage
    {
        /*
         * TODO: CRITICAL! CONTINUE. Obviously I should customize the message template. Make it dark in the brand colors, etc. Ignore Markdown emails for now - YAGNI.
         *
         * TODO: CRITICAL! I should, of course, use localized strings here as well. And test that localization works. Note: User model should have the method to retrieve the user's locale. I've already added a placeholder for it. See details: https://laravel.com/docs/8.x/notifications#user-preferred-locales
         */
        return (new MailMessage)
            ->error()
            ->greeting('Oy! Michman reporting.')
            ->line("Something went wrong when performing a deployment of your project {$this->deployment->project->projectName}.")
            ->action(
                'View Deployments',
                route('projects.show', [$this->deployment->project, 'deployment'])
            );
    }

    /** Get the notification message to show in the UI. */
    public static function message(array $data = []): string
    {
        $deployment = static::deployment($data);
        $type = get_called_class();

        return __("notifications.messages.{$type}", [
            'project' => $deployment->project->projectName,
        ]);
    }

    /** Get the details view to display this notification in the UI. */
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
