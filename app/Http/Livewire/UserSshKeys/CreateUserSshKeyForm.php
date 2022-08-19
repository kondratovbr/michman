<?php declare(strict_types=1);

namespace App\Http\Livewire\UserSshKeys;

use App\Actions\UserSshKeys\StoreUserSshKeyAction;
use App\DataTransferObjects\UserSshKeyDto;
use App\Facades\Auth;
use App\Models\UserSshKey;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: IMPORTANT! Cover with tests.

class CreateUserSshKeyForm extends LivewireComponent
{
    use AuthorizesRequests;

    public array $state = [
        'name' => '',
        'public_key' => '',
    ];

    public function rules(): array
    {
        return [
            'state.name' => Rules::string(1, 255)->required(),
            'state.public_key' => Rules::sshPublicKey()->required(),
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', [UserSshKey::class, user()]);
    }

    /** Store a new user's SSH key. */
    public function store(StoreUserSshKeyAction $action): void
    {
        $state = $this->validate()['state'];

        $this->authorize('create', [UserSshKey::class, user()]);

        $action->execute(UserSshKeyDto::fromArray($state), user());

        $this->reset();

        $this->emit('user-ssh-key-stored');
    }

    public function render(): View
    {
        return view('user-ssh-keys.create-user-ssh-key-form');
    }
}
