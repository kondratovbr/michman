<?php declare(strict_types=1);

namespace App\Http\Livewire\UserSshKeys;

use App\Actions\UserSshKeys\AddUserSshKeyToOwnerServersAction;
use App\Actions\UserSshKeys\DeleteUserSshKeyAction;
use App\Actions\UserSshKeys\FullyDeleteUserSshKeyAction;
use App\Broadcasting\UserChannel;
use App\Events\UserSshKeys\UserSshKeyCreatedEvent;
use App\Events\UserSshKeys\UserSshKeyDeletedEvent;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\UserSshKey;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: VERY IMPORTANT! Make some progress indicators here as well. A simple spinner would do. For deletion and adding.

class UserSshKeysIndexTable extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

    public Collection $keys;

    protected $listeners = [
        'user-ssh-key-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            UserChannel::name(user()),
            [
                UserSshKeyCreatedEvent::class,
                UserSshKeyDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [UserSshKey::class, user()]);
    }

    /** Add an existing key to all existing servers of the user. */
    public function addToAllServers(string $id, AddUserSshKeyToOwnerServersAction $action): void
    {
        $userSshKey = UserSshKey::validated($id, $this->keys);

        $this->authorize('update', $userSshKey);

        $action->execute($userSshKey);
    }

    /** Remove a user's SSH key only from the database, leaving it on the servers. */
    public function removeFromMichman(string $id, DeleteUserSshKeyAction $action): void
    {
        $userSshKey = UserSshKey::validated($id, $this->keys);

        $this->authorize('delete', $userSshKey);

        $action->execute($userSshKey);
    }

    /** Remove a user's SSH key from the database as well as from all the servers. */
    public function removeFromServers(string $id, FullyDeleteUserSshKeyAction $action): void
    {
        $userSshKey = UserSshKey::validated($id, $this->keys);

        $this->authorize('delete', $userSshKey);

        $action->execute($userSshKey);
    }

    public function render(): View
    {
        $this->keys = user()->userSshKeys()->oldest()->get();

        return view('user-ssh-keys.user-ssh-keys-index-table');
    }
}
