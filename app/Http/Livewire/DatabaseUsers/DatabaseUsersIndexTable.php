<?php declare(strict_types=1);

namespace App\Http\Livewire\DatabaseUsers;

use App\Actions\DatabaseUsers\DeleteDatabaseUserAction;
use App\Actions\DatabaseUsers\UpdateDatabaseUserAction;
use App\Broadcasting\ServerChannel;
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
use Livewire\Component as LivewireComponent;

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
    /** @var bool[] [databaseKey => (bool) true] - databases to grant access to for the user. */
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
            ServerChannel::name($this->server),
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
            'password' => Rules::alphaNumDashString(8, 255)->nullable(),
            'grantedDatabases' => Rules::array(),
            'grantedDatabases.*' => Rules::integer()
                ->in($this->server->databases->pluck('id')->toArray()),
        ];
    }

    public function mount(): void
    {
        $this->authorize('index', [DatabaseUser::class, $this->server]);
    }

    /** Delete a database user. */
    public function delete(DeleteDatabaseUserAction $deleteAction, string $databaseUserKey): void
    {
        $databaseUser = DatabaseUser::validated($databaseUserKey, $this->databaseUsers);

        $this->authorize('delete', $databaseUser);

        $deleteAction->execute($databaseUser);
    }

    /** Open database user updating dialog. */
    public function openModal(string $databaseUserKey): void
    {
        $this->updatingUser = DatabaseUser::validated($databaseUserKey, $this->databaseUsers);

        $this->authorize('update', $this->updatingUser);

        $this->password = '';
        $this->grantedDatabases = $this->updatingUser->databases
            ->keyBy(Database::keyName())
            ->map(fn(Database $database) => true)
            ->toArray();

        $this->resetErrorBag();
        $this->dispatchBrowserEvent('updating-database-user');
        $this->modalOpen = true;
    }

    /** Update the database user. */
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

        $this->modalOpen = false;

        $this->emit('database-user-stored');
        $this->emit('database-updated');
    }

    public function render(): View
    {
        $this->databases = $this->server->databases()->oldest()->get();
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('database-users.index-table');
    }
}
