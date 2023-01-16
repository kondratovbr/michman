<?php declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

class BrowserEvents extends LivewireComponent
{
    /** Trigger browser events stored on User. */
    public function triggerEvents(): void
    {
        if (guest())
            return;

        $user = user();

        foreach ($user->browserEvents ?? [] as $event) {
            $this->emit($event['type'], $event['name'], $event['payload']);
        }

        $user->browserEvents = null;
        $user->save();
    }

    public function render(): View
    {
        return view('livewire.browser-events');
    }
}
