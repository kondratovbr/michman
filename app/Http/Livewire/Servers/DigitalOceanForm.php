<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DigitalOceanForm extends Component
{
    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('servers.digital-ocean-form');
    }
}
