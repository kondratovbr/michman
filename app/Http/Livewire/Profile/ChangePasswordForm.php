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
    /** Currently typed user's current password. */
    public string $current_password = '';
    /** Currently typed new password. */
    public string $password = '';
    /** Currently typed new password confirmation. */
    public string $password_confirmation = '';

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'current_password' => Rules::currentUserPassword()->required(),
            'password' => Rules::genericPassword()->required()->confirmed(),
        ];
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatesUserPasswords $updater): void
    {
        $this->validate();

        $updater->update(Auth::user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

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
