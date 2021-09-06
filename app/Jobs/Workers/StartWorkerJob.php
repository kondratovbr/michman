<?php declare(strict_types=1);

namespace App\Jobs\Workers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Worker;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartWorkerScript;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// TODO: CRITICAL! Test this job and the script and cover with tests.

class StartWorkerJob extends AbstractRemoteServerJob
{
    protected Worker $worker;

    public function __construct(Worker $worker)
    {
        parent::__construct($worker->server);

        $this->worker = $worker->withoutRelations();
    }

    public function handle(StartWorkerScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->server->freshLockForUpdate();
            $worker = $this->worker->freshLockForUpdate();

            if ($worker->isActive()) {
                Log::warning('StartWorkerJob: This worker is already marked as active. Worker ID: ' . $worker->id);
                return;
            }

            try {
                $success = $script->execute($server, $worker);
                $worker->status = $success ? Worker::STATUS_ACTIVE : Worker::STATUS_FAILED;
            } catch (ServerScriptException) {
                $worker->status = Worker::STATUS_FAILED;
            } finally {
                $worker->save();
            }
        }, 5);
    }
}
