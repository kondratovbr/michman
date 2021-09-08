<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\DataTransferObjects\DaemonData;
use App\Jobs\Daemons\StartDaemonJob;
use App\Models\Daemon;
use App\Models\Server;

// TODO: CRITICAL! Cover with tests!

class StoreDaemonAction
{
    public function execute(DaemonData $data, Server $server): Daemon
    {
        /** @var Daemon $daemon */
        $daemon = $server->daemons()->make($data->toArray());

        $daemon->status = Daemon::STATUS_STARTING;

        $daemon->save();

        StartDaemonJob::dispatch($daemon);

        return $daemon;
    }
}
