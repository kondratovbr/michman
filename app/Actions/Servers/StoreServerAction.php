<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\Servers\AddServerSshKeyToProviderJob;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\Servers\VerifyRemoteServerIsSuitableJob;
use App\Jobs\Servers\UpdateServerAvailabilityJob;
use App\Models\Server;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;

class StoreServerAction
{
    public function execute(NewServerData $data): Server
    {
        $attributes = $data->toArray();
        $attributes['ssh_port'] = (string) config('servers.default_ssh_port');
        $attributes['sudo_password'] = Str::random(32);

        /** @var Server $server */
        $server = $data->provider->servers()->create($attributes);

        Bus::chain([
            // TODO: CRITICAL! Currently this doesn't entirely work. I should either have a user with passwordless sudo or actually store the sudo password encrypted in the DB.
            new CreateWorkerSshKeyForServerJob($server),
            new AddServerSshKeyToProviderJob($server),
            new RequestNewServerFromProviderJob($server, $data),
            new GetServerPublicIpJob($server),
            new VerifyRemoteServerIsSuitableJob($server),
            new PrepareRemoteServerJob($server),
            new UpdateServerAvailabilityJob($server),

            // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

        ])->dispatch();

        return $server;
    }
}
