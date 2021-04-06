<?php declare(strict_types=1);

namespace App\Http\Livewire\Providers;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class IndexTable extends Component
{
    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('providers.index-table');
    }
}
