<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;

class UpdateDaemonsStatusesAction
{
    public function execute(Server $server): void
    {
        $jobs = $server->daemons->map(
            fn(Daemon $daemon) => new UpdateDaemonStateJob($daemon)
        );

        Bus::batch($jobs)
            ->onQueue($jobs->first()->queue)
            ->allowFailures()
            ->dispatch();
    }
}
