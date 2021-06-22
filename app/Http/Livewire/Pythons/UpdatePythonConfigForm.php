<?php declare(strict_types=1);

namespace App\Http\Livewire\Pythons;

use Livewire\Component as LivewireComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\View\View;

class UpdatePythonConfigForm extends LivewireComponent
{
    use AuthorizesRequests;

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('pythons.update-config-form');
    }
}
