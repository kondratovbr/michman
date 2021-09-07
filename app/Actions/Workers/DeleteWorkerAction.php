<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\DeleteWorkerJob;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class DeleteWorkerAction
{
    public function execute(Worker $worker): void
    {
        DB::transaction(function () use ($worker) {
            $worker = $worker->freshLockForUpdate();

            $worker->status = Worker::STATUS_DELETING;
            $worker->save();

            DeleteWorkerJob::dispatch($worker);
        }, 5);
    }
}
