<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class TestEmailNotification extends AbstractNotification
{
    protected bool $mail = true;

    public function __construct(
        public string $message = 'Test notification',
    ) {
        parent::__construct();
    }

    /** Configure the mail representation of the notification. */
    protected function mail(MailMessage $mail, User $notifiable): MailMessage
    {
        return $mail
            ->success()
            ->line($this->message);
    }

    public function toArray(User $notifiable): array
    {
        return [
            'message' => $this->message,
        ];
    }

    /** Get the data for localized email strings for this notification. */
    protected function dataForMail(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
