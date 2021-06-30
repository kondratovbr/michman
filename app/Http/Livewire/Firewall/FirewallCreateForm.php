<?php declare(strict_types=1);

namespace App\Http\Livewire\Firewall;

use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

class FirewallCreateForm extends LivewireComponent
{
    public function mount(): void
    {
        //
    }

    public function render(): View
    {
        return view('firewall.create-form');
    }
}
