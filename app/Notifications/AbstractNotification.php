<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// TODO: IMPORTANT! Cover with tests somehow.

abstract class AbstractNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var bool Indicates if this notification should be sent via email. */
    protected bool $mail = false;
    /** @var bool Indicates if this notification should be broadcasted. */
    protected bool $broadcast = true;

    public function __construct()
    {
        // https://laravel.com/docs/8.x/notifications#queued-notifications-and-database-transactions
        $this->afterCommit = true;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return string[]
     */
    public function via(User $notifiable): array
    {
        $via = ['database'];

        if ($this->mail)
            $via[] = 'mail';

        if ($this->broadcast)
            $via[] = 'broadcast';

        return $via;
    }

    /** Get the mail representation of the notification. */
    public function toMail(User $notifiable): MailMessage
    {
        $type = get_called_class();
        $data = $this->dataForMail();

        $mail = (new MailMessage)->theme('dark');

        foreach ([
            'subject',
            'greeting',
        ] as $element) {
            if ($string = trans_try([
                "notifications.mails.$type.$element",
                "notifications.mails.default.$element",
            ], $data)) {
                $mail->$element($string);
            }
        }

        foreach (trans()->get("notifications.mails.$type.lines") as $key => $line) {
            $mail->line(__("notifications.mails.$type.lines.$key", $data));
        }

        $mail->action(__('notifications.mails.default.action'), route('home'));

        return $this->mail($mail, $notifiable);
    }

    /** Get a translated string for an email. */
    protected function transMail(string $key): string
    {
        $type = get_called_class();

        return __("notifications.mails.$type.$key", $this->dataForMail());
    }

    /** Get the broadcastable representation of the notification. */
    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toArray($notifiable)))
            ->onQueue('broadcasting');
    }

    /** Get the notification message to show in the UI. */
    public static function message(array $data = []): string
    {
        $type = get_called_class();

        return __("notifications.messages.$type", static::dataForMessage($data));
    }

    /** Get the data for localized message strings for this notification. */
    protected static function dataForMessage(array $data = []): array
    {
        return [];
    }

    /** Get the data for localized email strings for this notification. */
    protected function dataForMail(): array
    {
        return [];
    }

    /**
     * Get the array representation of the notification.
     *
     * Used for database storage and for broadcasting.
     */
    abstract public function toArray(User $notifiable): array;

    /** Compile a notification email. */
    protected function mail(MailMessage $mail, User $notifiable): MailMessage
    {
        return $mail;
    }
}
