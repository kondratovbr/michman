<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartDaemonScript;
use App\States\Daemons\Failed;
use App\States\Daemons\Starting;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

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
            // TODO: CRITICAL! CONTINUE. Change the rest of my server locking on jobs to shared locks
            //       in cases where I don't actually update the server model,
            //       i.e. mostly where I'm just reading SSH parameters from it.
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            if (! $daemon->state->is(Starting::class))
                return;

            try {
                $script->execute($server, $daemon);
            } catch (ServerScriptException) {
                $daemon->state->transitionTo(Failed::class);
                return;
            }

            UpdateDaemonStateJob::dispatch($daemon);
        }, 5);
    }
}
