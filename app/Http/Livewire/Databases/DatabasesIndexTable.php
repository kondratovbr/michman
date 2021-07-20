<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Actions\Databases\DeleteDatabaseAction;
use App\Broadcasting\ServersChannel;
use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\Databases\DatabaseDeletedEvent;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Database;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Add a confirmation window on database deletion and make sure a database that's in use by an active project cannot be deleted. See how Forge does it.

class DatabasesIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $databases;
    public Collection $databaseUsers;

    protected $listeners = [
        'database-stored' => '$refresh',
        'database-updated' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServersChannel::name($this->server),
            [
                DatabaseCreatedEvent::class,
                DatabaseUpdatedEvent::class,
                DatabaseDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('index', [Database::class, $this->server]);
    }

    /**
     * Delete a database.
     */
    public function delete(DeleteDatabaseAction $action, string $databaseKey): void
    {
        $databaseKey = Validator::make(
            ['database_key' => $databaseKey],
            ['database_key' => Rules::string(1, 16)
                ->in($this->databases->modelKeys())
                ->required()],
        )->validate()['database_key'];

        /** @var Database $database */
        $database = $this->server->databases()->findOrFail($databaseKey);

        $this->authorize('delete', $database);

        $action->execute($database);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->databases = $this->server->databases()->oldest()->get();
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('databases.index-table');
    }
}
