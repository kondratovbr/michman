<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateForm extends Component
{
    public string $provider = 'digital_ocean_v2';
    public string $token = '';
    public string $key = '';
    public string $secret = '';
    public string $name = '';

    /**
     * Store a new server provider credentials.
     */
    public function store(): void
    {
        //
        // This event is used to show the success message.
        $this->emit('saved');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('providers.create-form');
    }
}
