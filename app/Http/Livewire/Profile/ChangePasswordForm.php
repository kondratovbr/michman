<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Models\User;
use App\Facades\Auth;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Livewire\Component;

class ChangePasswordForm extends Component
{
    use AuthorizesRequests;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Get the validation rules.
     */
    protected function rules(): array
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
        $validated = $this->validate();

        $this->authorize('changePassword', [Auth::user()]);

        $updater->update(Auth::user(), [
            'current_password' => $validated['current_password'],
            'password' => $validated['password'],
            'password_confirmation' => $validated['password_confirmation'],
        ]);

        $this->reset();

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
