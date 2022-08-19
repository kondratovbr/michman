<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Facades\Auth;
use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;

class OAuthLinkForm extends LivewireComponent
{
    /** OAuth provider name as in config. */
    public string $provider;

    public bool $confirmationModalOpen = false;

    /** Check if the user can unlink an OAuth provider. */
    public function getCanUnlinkProperty(): bool
    {
        $user = user();

        if ($user->usesPassword())
            return true;

        return $user->oauthUsers()->count() > 1;
    }

    /** Get a localized OAuth provider name. */
    public function getProviderLabelProperty(): string
    {
        return __("auth.oauth.providers.{$this->provider}.label") ?? $this->provider;
    }

    public function render(): View
    {
        return view('profile.oauth-link-form');
    }
}
