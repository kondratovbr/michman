<?php declare(strict_types=1);

namespace App\Http\Livewire\Daemons;

use App\Broadcasting\ServerChannel;
use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Daemon;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class DaemonsIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $daemons;

    /** @var string[] */
    protected $listeners = [
        'daemon-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServerChannel::name($this->server),
            [
                DaemonCreatedEvent::class,
                DaemonUpdatedEvent::class,
                DaemonDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('create', [Daemon::class, $this->server]);
    }

    /**
     * Store the newly configured daemon.
     */
    public function store(): void
    {
        //
    }

    public function render(): View
    {
        $this->daemons = $this->server->daemons()->oldest()->get();

        return view('daemons.daemons-index-table');
    }
}
