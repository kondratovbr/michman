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

    /** Configure the mail representation of the notification. */
    protected function mail(MailMessage $mail, User $notifiable): MailMessage
    {
        return $mail
            ->error()
            ->action(
                $this->transMail('action'),
                route('projects.show', [$this->deployment->project, 'deployment'])
            );
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
