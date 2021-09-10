<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Jobs\Daemons\StartDaemonJob;
use App\Models\Daemon;
use App\States\Daemons\Starting;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class StartDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();

            if (! $daemon->state->canTransitionTo(Starting::class))
                return;

            $daemon->state->transitionTo(Starting::class);

            StartDaemonJob::dispatch($daemon);
        }, 5);
    }
}
