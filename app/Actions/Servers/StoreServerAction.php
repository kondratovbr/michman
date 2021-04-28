<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;

class StoreServerAction
{
    public function execute(NewServerData $data): Server
    {
        /** @var Server $server */
        $server = $data->provider->servers()->create($data->toArray());

        Bus::chain([
            new RequestNewServerFromProviderJob($server),
            // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!
        ])->dispatch();

        return $server;
    }
}
