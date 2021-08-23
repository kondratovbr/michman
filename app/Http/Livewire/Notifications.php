<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Auth;
use App\Models\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component as LivewireComponent;

class Notifications extends LivewireComponent
{
    public Collection $notifications;

    public bool $modalOpen = false;
    /** Currently viewed notification. */
    public Notification $notification;

    /**
     * Get a notification-specific details view.
     */
    public function getDetailsViewProperty(): View
    {
        return $this->notification->detailsView();
    }

    /**
     * Open a modal with the details about a notification.
     */
    public function details(string $id): void
    {
        $this->notification = $this->notifications->firstWhere('id', $id);

        $this->modalOpen = true;
    }

    /**
     * Trash a notification, i.e. mark it as read.
     */
    public function trash(string $id): void
    {
        //
    }

    public function render(): View
    {
        // TODO: Can I use caching here somehow so we don't reload this each time? And how to invalidate it then?
        $this->notifications = Auth::user()->unreadNotifications()->latest()->get();

        ray($this->notifications);

        return view('livewire.notifications');
    }
}
