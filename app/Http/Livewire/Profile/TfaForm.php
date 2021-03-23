<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Illuminate\Contracts\View\View;
use App\Facades\Auth;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Features;
use Laravel\Jetstream\ConfirmsPasswords;
use Livewire\Component;

/**
 * @property-read User $user
 * @property-read bool $enabled
 */
class TfaForm extends Component
{
    use ConfirmsPasswords;

    /** Indicates if two factor authentication QR code is being displayed. */
    public bool $showingQrCode = false;

    /** Indicates if two factor authentication recovery codes are being displayed. */
    public bool $showingRecoveryCodes = false;

    /**
     * Enable two factor authentication for the user.
     */
    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable): void
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        // We're just going to use a built-int action class by Fortify.
        $enable(Auth::user());

        $this->showingQrCode = true;
        $this->showingRecoveryCodes = true;
    }

    /**
     * Display the user's recovery codes.
     */
    public function showRecoveryCodes(): void
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $this->showingRecoveryCodes = true;
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate): void
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $generate(Auth::user());

        $this->showingRecoveryCodes = true;
    }

    /**
     * Disable two factor authentication for the user.
     */
    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable): void
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $disable(Auth::user());
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): User
    {
        return Auth::user();
    }

    /**
     * Determine if two factor authentication is enabled.
     */
    public function getEnabledProperty(): bool
    {
        return ! empty($this->user->two_factor_secret);
    }

    public function render(): View
    {
        return view('profile.tfa-form');
    }
}
