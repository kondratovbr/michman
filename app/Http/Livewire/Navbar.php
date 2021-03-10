<?php declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Navbar extends Component
{
    /** @var string[] The component's listeners. */
    protected $listeners = [
        'refresh-navigation-menu' => '$refresh',
    ];

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.navbar');
    }
}
