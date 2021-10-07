<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Validation\Rules;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use App\Facades\Auth;
use Laravel\Jetstream\Contracts\DeletesUsers;
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
    public function deleteUser(DeletesUsers $deleter, StatefulGuard $auth): RedirectResponse
    {
        // TODO: CRITICAL! Implement the actual feature. Would be more complex than that. Maybe need to clean servers, logout providers, VCSs, etc. Maybe need to have a cooldown time, so a user can stop deletion, if necessary. Also, need to handle billing on deletion somehow.

        abort(403);

        $this->authorize('deleteAccount', [Auth::user()]);

        $this->validate();

        $deleter->delete(Auth::user()->fresh());

        $auth->logout();

        return redirect('/');
    }

    public function render(): View
    {
        return view('profile.delete-account-form');
    }
}
