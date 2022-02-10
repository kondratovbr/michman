<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Root\StopDaemonScript;
use App\States\Daemons\Deleting;
use Illuminate\Support\Facades\DB;

class DeleteDaemonJob extends AbstractRemoteServerJob
{
    protected Daemon $daemon;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemon = $daemon->withoutRelations();
    }

    public function handle(StopDaemonScript $stop): void
    {
        DB::transaction(function () use ($stop) {
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            if (! $daemon->state->is(Deleting::class))
                return;

            $stop->execute($server, $daemon);

            $daemon->purge();
        }, 5);
    }
}
