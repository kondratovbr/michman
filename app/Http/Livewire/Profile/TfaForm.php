<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TfaForm extends Component
{
    public function render(): View
    {
        return view('profile.tfa-form');
    }
}
