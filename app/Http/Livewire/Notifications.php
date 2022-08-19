<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Broadcasting\UserChannel;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Notification;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

class Notifications extends LivewireComponent
{
    use ListensForEchoes;

    public Collection $notifications;

    public bool $modalOpen = false;
    /** Currently viewed notification. */
    public Notification $notification;

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            UserChannel::name(user()),
            BroadcastNotificationCreated::class,
            '$refresh',
        );
    }

    /** Get a notification-specific details view. */
    public function getDetailsViewProperty(): View
    {
        return $this->notification->detailsView();
    }

    /** Open a modal with the details about a notification. */
    public function details(string $id): void
    {
        $notification = $this->validatedNotification($id);

        if (! $notification->viewable())
            return;

        $this->notification = $notification;

        $this->modalOpen = true;
    }

    /** Trash a notification, i.e. mark it as read. */
    public function trash(string $id): void
    {
        $notification = $this->validatedNotification($id);

        $notification->markAsRead();
    }

    protected function validatedNotification(string $id): Notification
    {
        $id = Validator::make(
            ['id' => $id],
            ['id' => Rules::uuid()
                ->in($this->notifications->modelKeys())
                ->required()],
        )->validate()['id'];

        return $this->notifications->firstWhere('id', $id);
    }

    public function render(): View
    {
        // TODO: Can I use caching here somehow so we don't reload this each time? And how to invalidate it then?
        $this->notifications = user()->unreadNotifications()->latest()->get();

        return view('livewire.notifications');
    }
}
