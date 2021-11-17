<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

class OAuthLinkForm extends LivewireComponent
{
    /** OAuth provider name as in config */
    public string $provider;

    public function render(): View
    {
        return view('profile.oauth-link-form');
    }
}
