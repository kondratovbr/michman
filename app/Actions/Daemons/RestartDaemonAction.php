<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Models\Daemon;
use App\States\Daemons\Restarting;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class RestartDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();

            if (! $daemon->state->canTransitionTo(Restarting::class))
                return;

            // TODO: CRITICAL! CONTINUE. Implement.

            //
        }, 5);
    }
}
