<?php declare(strict_types=1);

namespace App\Jobs\Workers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Worker;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartWorkerScript;
use App\Scripts\Root\StopWorkerScript;
use App\States\Workers\Active;
use App\States\Workers\Failed;
use App\States\Workers\Starting;
use Illuminate\Support\Facades\DB;

class RestartWorkerJob extends AbstractRemoteServerJob
{
    protected Worker $worker;

    public function __construct(Worker $worker)
    {
        parent::__construct($worker->server);

        $this->worker = $worker->withoutRelations();
    }

    public function handle(StopWorkerScript $stop, StartWorkerScript $start): void
    {
        DB::transaction(function () use ($stop, $start) {
            $server = $this->server->freshSharedLock();
            $worker = $this->worker->freshLockForUpdate();

            if (! $worker->state->is(Starting::class))
                return;

            $ssh = $server->sftp();

            $stop->execute($server, $worker, $ssh);

            try {
                $success = $start->execute($server, $worker, $ssh);
                $worker->state->transitionTo($success ? Active::class : Failed::class);
            } catch (ServerScriptException) {
                $worker->state->transitionTo(Failed::class);
            }
        });
    }
}
