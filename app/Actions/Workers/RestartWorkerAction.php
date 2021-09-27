<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\RestartWorkerJob;
use App\Models\Worker;
use App\States\Workers\Starting;
use Illuminate\Support\Facades\DB;

class RestartWorkerAction
{
    public function execute(Worker $worker): void
    {
        DB::transaction(function () use ($worker) {
            $worker = $worker->freshLockForUpdate();

            if (! $worker->state->canTransitionTo(Starting::class))
                return;

            $worker->state->transitionTo(Starting::class);

            RestartWorkerJob::dispatch($worker);
        }, 5);
    }
}
