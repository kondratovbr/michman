<?php declare(strict_types=1);

namespace App\Http\Livewire\Daemons;

use App\Actions\Daemons\DeleteDaemonAction;
use App\Actions\Daemons\RestartDaemonAction;
use App\Actions\Daemons\StartDaemonAction;
use App\Actions\Daemons\StopDaemonAction;
use App\Actions\Daemons\UpdateDaemonsStatusesAction;
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

/*
 * TODO: CRITICAL! CONTINUE. Implement the log viewer here. See Workers as well.
 */

// TODO: CRITICAL! Cover with tests!

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
        $this->authorize('index', [Daemon::class, $this->server]);
    }

    /**
     * Update the daemon's statuses.
     */
    public function updateStatuses(UpdateDaemonsStatusesAction $action): void
    {
        $this->authorize('index', [Daemon::class, $this->server]);

        $action->execute($this->server);
    }

    /**
     * Stop a running daemon.
     */
    public function stop(string $daemonKey, StopDaemonAction $action): void
    {
        $daemon = Daemon::validated($daemonKey, $this->daemons);

        $this->authorize('update', [Daemon::class, $daemon]);

        $action->execute($daemon);
    }

    /**
     * Start a stopped daemon.
     */
    public function start(string $daemonKey, StartDaemonAction $action): void
    {
        $daemon = Daemon::validated($daemonKey, $this->daemons);

        $this->authorize('update', [Daemon::class, $daemon]);

        $action->execute($daemon);
    }

    /**
     * Restart a daemon.
     */
    public function restart(string $daemonKey, RestartDaemonAction $action): void
    {
        $daemon = Daemon::validated($daemonKey, $this->daemons);

        $this->authorize('update', [Daemon::class, $daemon]);

        $action->execute($daemon);
    }

    /**
     * Stop and delete a daemon.
     */
    public function delete(string $daemonKey, DeleteDaemonAction $action): void
    {
        $daemon = Daemon::validated($daemonKey, $this->daemons);

        $this->authorize('delete', [Daemon::class, $daemon]);

        $action->execute($daemon);
    }

    public function render(): View
    {
        $this->daemons = $this->server->daemons()->oldest()->get();

        return view('daemons.daemons-index-table');
    }
}
