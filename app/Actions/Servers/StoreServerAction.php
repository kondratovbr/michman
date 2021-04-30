<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\Providers\AddServerSshKeyToProviderJob;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;

class StoreServerAction
{
    public function __construct(
        //
    ) {}

    public function execute(NewServerData $data): Server
    {
        $attributes = $data->toArray();
        $attributes['ssh_port'] = (string) config('servers.default_ssh_port');

        /** @var Server $server */
        $server = $data->provider->servers()->create($attributes);

        Bus::chain([
            new CreateWorkerSshKeyForServerJob($server),
            new AddServerSshKeyToProviderJob($server),
            new RequestNewServerFromProviderJob($server, $data),
            new GetServerPublicIpJob($server),

            // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

        ])->dispatch();

        return $server;
    }
}
