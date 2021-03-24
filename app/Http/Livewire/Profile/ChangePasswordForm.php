<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Models\User;
use App\Facades\Auth;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Livewire\Component;

class ChangePasswordForm extends Component
{
    /** The component's current state. */
    public array $state = [
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'current_password' => Rules::currentUserPassword()->required(),
        ];
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatesUserPasswords $updater): void
    {
        dd('Foobar?');

        $this->validate();

        $updater->update(Auth::user(), $this->state);

        $this->state = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $this->emit('saved');
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): User
    {
        return Auth::user();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('profile.change-password-form');
    }
}
