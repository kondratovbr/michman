<?php declare(strict_types=1);

namespace App\Jobs\Workers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Worker;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartWorkerScript;
use App\Scripts\Root\StopWorkerScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests. Not only the happy path.

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

            $ssh = $server->sftp();

            $stop->execute($server, $worker, $ssh);

            try {
                $success = $start->execute($server, $worker);
                $worker->status = $success ? Worker::STATUS_ACTIVE : Worker::STATUS_FAILED;
            } catch (ServerScriptException) {
                $worker->status = Worker::STATUS_FAILED;
            } finally {
                $worker->save();
            }
        }, 5);
    }
}
