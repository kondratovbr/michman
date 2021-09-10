<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\UpdateDaemonStateScript;
use App\States\Daemons\Deleting;
use App\States\Daemons\Failed;
use App\States\Daemons\Starting;
use App\States\Daemons\Stopped;
use App\States\Daemons\Stopping;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class UpdateDaemonStateJob extends AbstractRemoteServerJob
{
    use Batchable;

    protected Daemon $daemon;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemon = $daemon->withoutRelations();
    }

    public function handle(UpdateDaemonStateScript $script): void
    {
        DB::transaction(function () use($script) {
            $server = $this->server->freshSharedLock();
            $daemon = $this->daemon->freshLockForUpdate();

            /*
             * The daemon in one of these states isn't even on the server (or will be deleted soon),
             * i.e. there's no config for this daemon on the server,
             * so there's nothing to check the status of at all.
             */
            if ($daemon->state->is([
                Stopping::class,
                Stopped::class,
                Deleting::class,
            ])) {
                return;
            }

            try {
                $daemon->state = $script->execute($server, $daemon);
            } catch (ServerScriptException) {
                $daemon->state = Failed::class;
            }

            $daemon->save();

            // If the daemon is still starting, i.e. hasn't failed or successfully started yet -
            // repeat this job a bit later.
            if ($daemon->state->is(Starting::class))
                $this->release(30);
        }, 5);
    }
}
