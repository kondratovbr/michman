<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Auth;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Navbar extends Component
{
    /** @var string[] */
    protected $listeners = [
        // TODO: Do I use all of them? Probably only the first one.
        'refresh-navigation' => '$refresh',
        'refresh-navbar' => '$refresh',
        'refresh-navigation-menu' => '$refresh',
    ];

    /**
     * Render the component.
     */
    public function render(): View
    {
        $user = Auth::user()->load('servers');

        return view('livewire.navbar', [
            'user' => $user,
        ]);
    }
}
