<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

/*
 * TODO: CRITICAL! I saved this to show how Laravel intends to email notifications.
 *       I should implement emailing for the actual notifications and build those emails.
 */

class ExampleMailNotification extends AbstractNotification
{
    protected bool $mail = true;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray(User $notifiable): array
    {
        return [
            //
        ];
    }
}
