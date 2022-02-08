<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Actions\Users\DeleteUserAction;
use App\Validation\Rules;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Facades\Auth;
use Livewire\Component;

// TODO: CRITICAL! CONTINUE. Forgot to handle a situation when the user has no password (OAuth only).

class DeleteAccountForm extends Component
{
    use AuthorizesRequests;

    /** Indicates if user deletion is being confirmed. */
    public bool $confirmingUserDeletion = false;

    /** The user's currently typed password. */
    public string $password = '';

    protected function rules(): array
    {
        return [
            'password' => Rules::currentUserPassword()->required(),
        ];
    }

    /** Confirm that the user would like to delete their account. */
    public function confirmUserDeletion(): void
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatchBrowserEvent('confirming-delete-user');

        $this->confirmingUserDeletion = true;
    }

    /** Delete the current user. */
    public function deleteUser(DeleteUserAction $deleteUser, StatefulGuard $auth): void
    {
        $this->authorize('delete', [Auth::user()]);

        $this->validate();

        $deleteUser->execute(Auth::user()->fresh());

        $auth->logout();

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('profile.delete-account-form');
    }
}
