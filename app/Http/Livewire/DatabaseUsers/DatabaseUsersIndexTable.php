<?php declare(strict_types=1);

namespace App\Http\Livewire\DatabaseUsers;

use App\Actions\DatabaseUsers\DeleteDatabaseUserAction;
use App\Actions\DatabaseUsers\UpdateDatabaseUserAction;
use App\Broadcasting\ServersChannel;
use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Events\DatabaseUsers\DatabaseUserDeletedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Support\Arr;
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

    public Collection $databases;
    public Collection $databaseUsers;

    /** @var bool Indicates if a confirmation modal should currently be opened. */
    public bool $modalOpen = false;
    /** @var DatabaseUser A database user being updated in the updating dialog. */
    public DatabaseUser $updatingUser;
    /** @var string Currently typed new database user's password. */
    public string $password = '';
    /** @var bool[] Database key => (bool) true - databases to grant access to for the user. */
    public array $grantedDatabases = [];

    /** @var string[] */
    protected $listeners = [
        'database-user-stored' => '$refresh',
        'database-user-updated' => '$refresh',
        'database-stored' => '$refresh',
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

    protected function prepareForValidation($attributes): array
    {
        $attributes['grantedDatabases'] = Arr::keys(Arr::filter($attributes['grantedDatabases']));

        return $attributes;
    }

    public function rules(): array
    {
        return [
            'password' => Rules::alphaNumDashString(8, 255)->required(),
            'grantedDatabases' => Rules::array(),
            'grantedDatabases.*' => Rules::integer()
                ->in($this->server->databases->pluck('id')->toArray()),
        ];
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
    public function delete(DeleteDatabaseUserAction $deleteAction, string $databaseUserKey): void
    {
        $databaseUser = $this->getDatabaseUser($databaseUserKey);

        $this->authorize('delete', $databaseUser);

        $deleteAction->execute($databaseUser);
    }

    /**
     * Open database user updating dialog.
     */
    public function openModal(string $databaseUserKey): void
    {
        $this->updatingUser = $this->getDatabaseUser($databaseUserKey);

        $this->authorize('update', $this->updatingUser);

        $this->resetErrorBag();
        $this->password = '';
        $this->dispatchBrowserEvent('updating-database-user');
        $this->modalOpen = true;
    }

    /**
     * Update the database user.
     */
    public function update(UpdateDatabaseUserAction $updateAction): void
    {
        $validated = $this->validate();

        $this->authorize('update', $this->updatingUser);

        $updateAction->execute(
            $this->updatingUser,
            $validated['password'],
            Database::query()->findMany($validated['grantedDatabases']),
        );

        $this->reset(
            'password',
            'grantedDatabases',
        );

        $this->emit('database-user-stored');
        $this->emit('database-updated');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->databases = $this->server->databases()->oldest()->get();
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('database-users.index-table');
    }

    /**
     * Validate the database user key and fetch the corresponding model.
     */
    protected function getDatabaseUser(string $key): DatabaseUser
    {
        $key = Validator::make(
            ['database_user_key' => $key],
            ['database_user_key' => Rules::string(1, 16)
                ->in($this->databaseUsers->modelKeys())
                ->required()],
        )->validate()['database_user_key'];

        /** @var DatabaseUser $databaseUser */
        $databaseUser = $this->server->databaseUsers()->findOrFail($key);

        return $databaseUser;
    }
}
