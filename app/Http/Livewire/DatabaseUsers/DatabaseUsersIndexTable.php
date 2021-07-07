<?php declare(strict_types=1);

namespace App\Http\Livewire\DatabaseUsers;

use App\Actions\DatabaseUsers\DeleteDatabaseUserAction;
use App\Broadcasting\ServersChannel;
use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Events\DatabaseUsers\DatabaseUserDeletedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

// TODO: CRITICAL! Update the removal logic to account for DatabaseUser-to-Database connections and for the permissions on the server.

class DatabaseUsersIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $databaseUsers;

    /** @var string[] */
    protected $listeners = [
        'database-user-stored' => '$refresh',
        'database-user-updated' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServersChannel::name($this->server),
            [
                DatabaseUserCreatedEvent::class,
                DatabaseUserUpdatedEvent::class,
                DatabaseUserDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('index', [DatabaseUser::class, $this->server]);
    }

    /**
     * Delete a database user.
     */
    public function delete(DeleteDatabaseUserAction $action, string $databaseUserKey): void
    {
        $databaseUserKey = Validator::make(
            ['database_user_key' => $databaseUserKey],
            ['database_user_key' => Rules::string(1, 16)
                ->in($this->databaseUsers->modelKeys())
                ->required()],
        )->validate()['database_user_key'];

        /** @var DatabaseUser $databaseUser */
        $databaseUser = $this->server->databaseUsers()->findOrFail($databaseUserKey);

        $this->authorize('delete', $databaseUser);

        $action->execute($databaseUser);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('database-users.index-table');
    }
}
