<?php declare(strict_types=1);

namespace App\Jobs\Daemons;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Daemon;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartDaemonScript;
use App\States\Daemons\Failed;
use App\States\Daemons\Starting;
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
