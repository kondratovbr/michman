<?php declare(strict_types=1);

namespace App\Http\Livewire\UserSshKeys;

use App\Actions\UserSshKeys\StoreUserSshKeyAction;
use App\DataTransferObjects\UserSshKeyData;
use App\Facades\Auth;
use App\Models\UserSshKey;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

// TODO: CRITICAL! Don't forget to actually add these keys to new servers.

class CreateUserSshKeyForm extends LivewireComponent
{
    use AuthorizesRequests;

    public array $state = [
        'name' => '',
        'publicKey' => '',
    ];

    public function rules(): array
    {
        return [
            'name' => Rules::alphaNumDashString(1, 255)->required(),
            'publicKey' => Rules::sshPublicKey()->required(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', [UserSshKey::class, Auth::user()]);
    }

    /**
     * Store a new user's SSH key.
     */
    public function store(StoreUserSshKeyAction $action): void
    {
        $validated = $this->validate();

        $this->authorize('create', [UserSshKey::class, Auth::user()]);

        $action->execute(new UserSshKeyData(
            name: $validated['name'],
            publicKey: $validated['publicKey'],
        ), Auth::user());

        $this->reset();

        $this->emit('user-ssh-key-stored');
    }

    public function render(): View
    {
        return view('user-ssh-keys.create-user-ssh-key-form');
    }
}
