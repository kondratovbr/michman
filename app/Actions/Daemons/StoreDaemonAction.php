<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\DataTransferObjects\DaemonDto;
use App\Jobs\Daemons\StartDaemonJob;
use App\Models\Daemon;
use App\Models\Server;
use App\States\Daemons\Starting;

// TODO: CRITICAL! Cover with tests!

class StoreDaemonAction
{
    public function execute(DaemonDto $data, Server $server): Daemon
    {
        /** @var Daemon $daemon */
        $daemon = $server->daemons()->make($data->toArray());

        $daemon->state = Starting::class;

        $daemon->save();

        StartDaemonJob::dispatch($daemon);

        return $daemon;
    }
}
