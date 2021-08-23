<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

abstract class AbstractNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var bool Indicates if this notification should be sent via email. */
    protected bool $mail = false;
    /** @var bool Indicates if this notification should be broadcasted. */
    protected bool $broadcast = false;

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

        if ($this->mail) {
            Log::error('AbstractNotification: The notification is marked to be mailed, but the mailing is not implemented at all yet.');
            // $via[] = 'mail';
        }

        if ($this->broadcast)
            $via[] = 'broadcast';

        return $via;
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toArray($notifiable)))
            ->onQueue('broadcasting');
    }

    /**
     * Get the message to show in the UI.
     */
    public static function message(array $data = []): string
    {
        $type = get_called_class();

        return __("notifications.messages.{$type}");
    }

    /**
     * Get the array representation of the notification.
     *
     * Used for database storage and for broadcasting.
     */
    abstract public function toArray(User $notifiable): array;
}
