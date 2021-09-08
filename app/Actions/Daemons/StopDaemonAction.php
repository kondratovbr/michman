<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Jobs\Daemons\StopDaemonJob;
use App\Models\Daemon;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class StopDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();

            if ($daemon->isStatus([
                Daemon::STATUS_DELETING,
                Daemon::STATUS_STOPPED,
                Daemon::STATUS_FAILED,
            ])) {
                return;
            }

            $daemon->status = Daemon::STATUS_STOPPING;
            $daemon->save();

            StopDaemonJob::dispatch($daemon);
        }, 5);
    }
}
