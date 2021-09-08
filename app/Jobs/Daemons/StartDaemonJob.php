<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartDaemonScript;
use Illuminate\Support\Facades\DB;

class StartDaemonJob extends AbstractRemoteServerJob
{
    protected Daemon $daemon;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemon = $daemon->withoutRelations();
    }

    public function handle(StartDaemonScript $script): void
    {
        DB::transaction(function () use ($script) {
            // TODO: CRITICAL! Change the rest of my server locking on jobs to shared locks
            //       in cases where I don't actually update the server model,
            //       i.e. mostly where I'm just reading SSH parameters from it.
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            if ($daemon->isStatus([
                Daemon::STATUS_ACTIVE,
                Daemon::STATUS_STOPPING,
                Daemon::STATUS_DELETING,
            ])) {
                return;
            }

            try {
                $script->execute($server, $daemon);
            } catch (ServerScriptException) {
                $daemon->status = Daemon::STATUS_FAILED;
                $daemon->save();
                return;
            }

            UpdateDaemonStatusJob::dispatch($daemon);
        }, 5);
    }
}
