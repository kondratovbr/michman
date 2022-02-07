<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Actions\Users\DeleteUserAction;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Facades\Auth;
use Livewire\Component;

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
    public function deleteUser(DeleteUserAction $deleteUser): void
    {
        // TODO: CRITICAL! CONTINUE. Implement the actual feature. Would be more complex than that. Maybe need to clean servers, logout providers, VCSs, etc. Maybe need to have a cooldown time, so a user can stop deletion, if necessary. Also, need to handle billing on deletion somehow.

        $this->authorize('delete', [Auth::user()]);

        $this->validate();

        ray('Will delete user ' . Auth::user()->email)->die();

        $deleteUser->execute(Auth::user()->fresh());

        Auth::logout();

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('profile.delete-account-form');
    }
}
