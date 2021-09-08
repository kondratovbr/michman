<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Models\Daemon;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class StartDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();

            if ($daemon->isStatus([
                Daemon::STATUS_DELETING,
                Daemon::STATUS_STARTING,
            ])) {
                return;
            }

            // TODO: CRITICAL! CONTINUE. Implement.

            //
        }, 5);
    }
}
