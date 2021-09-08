<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Models\Daemon;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class DeleteDaemonAction
{
    public function execute(Daemon $daemon): void
    {
        DB::transaction(function () use ($daemon) {
            // TODO: CRITICAL! CONTINUE. Implement.

            //
        }, 5);
    }
}
