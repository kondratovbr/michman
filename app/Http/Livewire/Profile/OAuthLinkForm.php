<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! The "Unlink" function needs a confirmation window with a deep warning. And it shouldn't be available if some projects use the provider as VCS. And if it's the last auth method the user has.

class OAuthLinkForm extends LivewireComponent
{
    /** OAuth provider name as in config. */
    public string $provider;

    public function render(): View
    {
        return view('profile.oauth-link-form');
    }
}
