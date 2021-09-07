<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\RestartWorkerJob;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class RestartWorkerAction
{
    public function execute(Worker $worker): void
    {
        DB::transaction(function () use ($worker) {
            $worker = $worker->freshLockForUpdate();

            $worker->status = Worker::STATUS_STARTING;
            $worker->save();

            RestartWorkerJob::dispatch($worker);
        }, 5);
    }
}
