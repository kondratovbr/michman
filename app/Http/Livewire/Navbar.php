<?php declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Auth;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/*
 * TODO: CRITICAL! Make sure the navbar updates on server/project creation/deletion/update, etc. It does not right now.
 */

class Navbar extends Component
{
    /** @var string[] */
    protected $listeners = [
        // TODO: Do I use all of them? Probably only the first one.
        'refresh-navigation' => '$refresh',
        'refresh-navbar' => '$refresh',
        'refresh-navigation-menu' => '$refresh',
    ];

    public function render(): View
    {
        $user = Auth::user()->load('servers');

        return view('livewire.navbar', [
            'user' => $user,
        ]);
    }
}
