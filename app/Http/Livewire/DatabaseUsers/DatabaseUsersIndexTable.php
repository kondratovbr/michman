<?php declare(strict_types=1);

namespace App\Http\Livewire\DatabaseUsers;

use App\Broadcasting\ServersChannel;
use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Events\DatabaseUsers\DatabaseUserDeletedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class DatabaseUsersIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $databaseUsers;

    /** @var string[] */
    protected $listeners = [
        'database-user-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServersChannel::name($this->server),
            [
                DatabaseUserCreatedEvent::class,
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
        //
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
