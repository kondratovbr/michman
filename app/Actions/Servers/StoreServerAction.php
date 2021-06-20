<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\Servers\AddWorkerSshKeyToServerProviderJob;
use App\Jobs\Servers\AddServerSshKeyToVcsJob;
use App\Jobs\Servers\ConfigureServerJob;
use App\Jobs\Servers\CreateDatabaseJob;
use App\Jobs\Servers\CreateServerSshKeyJob;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\InstallCacheJob;
use App\Jobs\Servers\InstallDatabaseJob;
use App\Jobs\Servers\InstallPythonJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\Servers\UpdateUserSshKeysOnServerJob;
use App\Jobs\Servers\UploadServerSshKeyToServerJob;
use App\Jobs\Servers\VerifyRemoteServerIsSuitableJob;
use App\Jobs\Servers\UpdateServerAvailabilityJob;
use App\Models\Server;
use App\Models\User;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;

class StoreServerAction
{
    public function execute(NewServerData $data, User $user): Server
    {
        $attributes = $data->toArray();
        $attributes['ssh_port'] = (string) config('servers.default_ssh_port');
        $attributes['sudo_password'] = Str::random(32);

        /** @var Server $server */
        $server = $data->provider->servers()->create($attributes);

        /*
         * TODO: CRITICAL! Don't forget to:
         *       - Generate SSH keys on the server. Or rather generate locally and send to the server.
         *       - Add server's SSH keys to user's VCS if needed.
         *       - ...
         */

        $jobs = [

            new CreateWorkerSshKeyForServerJob($server),
            new AddWorkerSshKeyToServerProviderJob($server),
            new RequestNewServerFromProviderJob($server, $data),
            new GetServerPublicIpJob($server),
            new VerifyRemoteServerIsSuitableJob($server),
            new PrepareRemoteServerJob($server),
            new UpdateServerAvailabilityJob($server),
            new UpdateUserSshKeysOnServerJob($server),
            new InstallDatabaseJob($server, $data->database),
            new CreateDatabaseJob($server, $data->dbName),
            new InstallCacheJob($server, $data->cache),
            new InstallPythonJob($server, $data->pythonVersion),

            // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

        ];

        if ($data->addSshKeyToVcs) {
            // TODO: CRITICAL! CONTINUE! Don't forget to implement and test all of these!
            $jobs[] = new CreateServerSshKeyJob($server);
            $jobs[] = new UploadServerSshKeyToServerJob($server);
        }

        foreach ($user->vcsProviders as $vcsProvider)
            $jobs[] = new AddServerSshKeyToVcsJob($server, $vcsProvider);

        $jobs[] = new ConfigureServerJob($server);

        Bus::chain($jobs)->dispatch();

        return $server;
    }
}
