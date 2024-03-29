<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Jobs\Daemons\RestartDaemonJob;
use App\Models\Daemon;
use App\States\Daemons\Restarting;
use Illuminate\Support\Facades\DB;

class RestartDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();

            if (! $daemon->state->canTransitionTo(Restarting::class))
                return;

            $daemon->state->transitionTo(Restarting::class);

            RestartDaemonJob::dispatch($daemon);
        }, 5);
    }
}
