<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\DeleteWorkerJob;
use App\Models\Worker;
use App\States\Workers\Deleting;
use Illuminate\Support\Facades\DB;

class DeleteWorkerAction
{
    public function execute(Worker $worker, bool $returnJob = false): DeleteWorkerJob|null
    {
        return DB::transaction(function () use ($worker, $returnJob): DeleteWorkerJob|null {
            $worker = $worker->freshLockForUpdate();

            if (! $worker->state->canTransitionTo(Deleting::class))
                return null;

            $worker->state->transitionTo(Deleting::class);

            if ($returnJob)
                return new DeleteWorkerJob($worker);

            DeleteWorkerJob::dispatch($worker);

            return null;
        }, 5);
    }
}
