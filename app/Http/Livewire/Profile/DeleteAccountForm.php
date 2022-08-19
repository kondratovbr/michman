<?php declare(strict_types=1);

namespace App\Http\Livewire\Profile;

use App\Actions\Users\DeleteUserAction;
use App\Support\Str;
use App\Validation\Rules;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Facades\Auth;
use Livewire\Component;

// TODO: Cover with tests.

class DeleteAccountForm extends Component
{
    use AuthorizesRequests;

    /** Indicates if user deletion is being confirmed. */
    public bool $confirmingUserDeletion = false;

    public string $password = '';
    public string $email = '';

    protected function prepareForValidation($attributes): array
    {
        $attributes['email'] = Str::lower($attributes['email']);

        return $attributes;
    }

    protected function rules(): array
    {
        return [
            'password' => Rules::currentUserPassword()->requiredIf(fn() => user()->usesPassword()),
            'email' => Rules::currentUserEmail()->requiredIf(fn() => ! user()->usesPassword()),
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
        $this->authorize('delete', [user()]);

        $this->validate();

        $deleteUser->execute(user()->fresh());

        $auth->logout();

        $this->redirectRoute('home');
    }

    public function render(): View
    {
        return view('profile.delete-account-form');
    }
}
