<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Actions\Databases\DeleteDatabaseAction;
use App\Broadcasting\ServerChannel;
use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\Databases\DatabaseDeletedEvent;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: IMPORTANT! Add a confirmation window on database deletion and make sure a database that's in use by an active project cannot be deleted. See how Forge does it. Same for DB users.

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
            ServerChannel::name($this->server),
            [
                DatabaseCreatedEvent::class,
                DatabaseUpdatedEvent::class,
                DatabaseDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [Database::class, $this->server]);
    }

    /** Delete a database. */
    public function delete(DeleteDatabaseAction $action, string $databaseKey): void
    {
        $database = Database::validated($databaseKey, $this->databases);

        $this->authorize('delete', $database);

        $action->execute($database);
    }

    public function render(): View
    {
        $this->databases = $this->server->databases()->oldest()->get();
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('databases.index-table');
    }
}
