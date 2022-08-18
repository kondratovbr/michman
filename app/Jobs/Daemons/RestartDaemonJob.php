<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartDaemonScript;
use App\Scripts\Root\StopDaemonScript;
use App\States\Daemons\Failed;
use App\States\Daemons\Restarting;
use Illuminate\Support\Facades\DB;

class RestartDaemonJob extends AbstractRemoteServerJob
{
    protected Daemon $daemon;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemon = $daemon->withoutRelations();
    }

    public function handle(
        StopDaemonScript $stop,
        StartDaemonScript $start,
    ): void{
        DB::transaction(function () use ($stop, $start) {
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            if (! $daemon->state->is(Restarting::class))
                return;

            $ssh = $server->sftp();

            $stop->execute($server, $daemon, $ssh);

            try {
                $start->execute($server, $daemon, $ssh);
            } catch (ServerScriptException) {
                $daemon->state->transitionTo(Failed::class);
                return;
            }

            UpdateDaemonStateJob::dispatch($daemon);
        });
    }
}
