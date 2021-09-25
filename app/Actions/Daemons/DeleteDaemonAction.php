<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Jobs\Daemons\DeleteDaemonJob;
use App\Models\Daemon;
use App\States\Daemons\Deleting;
use Illuminate\Support\Facades\DB;

class DeleteDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            $daemon = $daemon->freshLockForUpdate();

            if (! $daemon->state->canTransitionTo(Deleting::class))
                return;

            $daemon->state->transitionTo(Deleting::class);

            DeleteDaemonJob::dispatch($daemon);
        }, 5);
    }
}
