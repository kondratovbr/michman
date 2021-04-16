<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Facades\Auth;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ServersIndexTable extends Component
{
    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('servers.index-table', [
            'servers' => Auth::user()->servers,
        ]);
    }
}
