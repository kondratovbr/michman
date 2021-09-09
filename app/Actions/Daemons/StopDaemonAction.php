<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Jobs\Daemons\StopDaemonJob;
use App\Models\Daemon;
use App\States\Daemons\Stopping;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class StopDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();
            
            if (! $daemon->state->canTransitionTo(Stopping::class))
                return;

            $daemon->state->transitionTo(Stopping::class);

            StopDaemonJob::dispatch($daemon);
        }, 5);
    }
}
