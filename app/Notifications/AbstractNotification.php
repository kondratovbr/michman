<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

abstract class AbstractNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var bool Indicates if this notification should be sent via email. */
    protected bool $mail = false;
    /** @var bool Indicates if this notification should be broadcasted. */
    protected bool $broadcast = false;

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

        // TODO: CRITICAL! Test if this system even works. Try to catch these notifications in Livewire.
        if ($this->broadcast)
            $via[] = 'broadcast';

        return $via;
    }

    /**
     * Get the array representation of the notification.
     *
     * Used for database storage and for broadcasting
     * if the method "toBroadcast" doesn't exist.
     */
    abstract public function toArray(User $notifiable): array;
}
