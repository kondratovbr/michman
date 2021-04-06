<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateForm extends Component
{
    /**
     * Store a new server provider credentials.
     */
    public function store(): void
    {
        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('providers.create-form');
    }
}
