<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\DeleteWorkerJob;
use App\Models\Worker;
use App\States\Workers\Deleting;
use Illuminate\Support\Facades\DB;

class DeleteWorkerAction
{
    public function execute(Worker $worker): void
    {
        DB::transaction(function () use ($worker) {
            $worker = $worker->freshLockForUpdate();

            if (! $worker->state->canTransitionTo(Deleting::class))
                return;

            $worker->state->transitionTo(Deleting::class);

            DeleteWorkerJob::dispatch($worker);
        }, 5);
    }
}
