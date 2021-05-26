<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\Servers\AddServerSshKeyToProviderJob;
use App\Jobs\Servers\ConfigureServerJob;
use App\Jobs\Servers\CreateDatabaseJob;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\InstallCacheJob;
use App\Jobs\Servers\InstallDatabaseJob;
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

        /*
         * TODO: CRITICAL! Don't forget to:
         *       - Install database if needed.
         *       - Install cache if needed.
         *       - Install Python if needed.
         *       - Install Nginx if needed.
         *       - Add existing user's SSH keys to the server.
         *       - Add server's SSH keys to user's VCS if needed.
         *       - ...
         */

        Bus::chain([

            new CreateWorkerSshKeyForServerJob($server),
            new AddServerSshKeyToProviderJob($server),
            new RequestNewServerFromProviderJob($server, $data),
            new GetServerPublicIpJob($server),
            new VerifyRemoteServerIsSuitableJob($server),
            new PrepareRemoteServerJob($server),
            new UpdateServerAvailabilityJob($server),
            new ConfigureServerJob($server),
            new InstallDatabaseJob($server, $data->database),
            new CreateDatabaseJob($server, $data->dbName),
            new InstallCacheJob($server, $data->cache),

            // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

        ])->dispatch();

        return $server;
    }
}
