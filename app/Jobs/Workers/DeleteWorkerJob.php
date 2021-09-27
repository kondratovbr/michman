<?php declare(strict_types=1);

namespace App\Jobs\Workers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Worker;
use App\Scripts\Root\StopWorkerScript;
use App\States\Workers\Deleting;
use Illuminate\Support\Facades\DB;

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

            if (! $worker->state->is(Deleting::class))
                return;

            $script->execute($server, $worker);

            $worker->delete();
        }, 5);
    }
}
