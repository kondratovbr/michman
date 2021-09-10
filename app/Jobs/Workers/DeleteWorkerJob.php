<?php declare(strict_types=1);

namespace App\Jobs\Workers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Worker;
use App\Scripts\Root\StopWorkerScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class DeleteWorkerJob extends AbstractRemoteServerJob
{
    protected Worker $worker;

    public function __construct(Worker $worker)
    {
        parent::__construct($worker->server);

        $this->worker = $worker->withoutRelations();
    }

    public function handle(StopWorkerScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->server->freshSharedLock();
            $worker = $this->worker->freshLockForUpdate();

            $script->execute($server, $worker);

            $worker->delete();
        }, 5);
    }
}
