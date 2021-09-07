<?php declare(strict_types=1);

namespace App\Actions\Daemons;

use App\DataTransferObjects\DaemonData;
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

        // TODO: CRITICAL! CONTINUE. Implement the job and dispatch it here.
        //       And then go and finally fix those effed up "state.name" validation messages.

        //

        return $daemon;
    }
}
