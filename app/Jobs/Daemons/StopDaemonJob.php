<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Root\StopDaemonScript;
use App\States\Daemons\Stopped;
use App\States\Daemons\Stopping;
use Illuminate\Support\Facades\DB;

class StopDaemonJob extends AbstractRemoteServerJob
{
    protected Daemon $daemon;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemon = $daemon->withoutRelations();
    }

    public function handle(StopDaemonScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            if (! $daemon->state->is(Stopping::class))
                return;

            $script->execute($server, $daemon);

            $daemon->state->transitionTo(Stopped::class);
        });
    }
}
