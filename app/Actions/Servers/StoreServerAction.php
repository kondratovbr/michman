<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\DataTransferObjects\NewServerData;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class StoreServerAction
{
    public function __construct(
        protected CreateWorkerSshKeyAction $createWorkerSshKeyAction
    ) {}

    public function execute(NewServerData $data): Server
    {
        // TODO: CRITICAL! CONTINUE!

        DB::beginTransaction();

        /** @var Server $server */
        $server = $data->provider->servers()->make($data->toArray());

        // TODO: Move this to a job?
        $this->createWorkerSshKeyAction->execute($server);

        DB::commit();

        Bus::chain([
            new RequestNewServerFromProviderJob($server, $data),
            // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!
        ])->dispatch();

        return $server;
    }
}
