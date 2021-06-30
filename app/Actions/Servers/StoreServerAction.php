<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\Servers\AddWorkerSshKeyToServerProviderJob;
use App\Jobs\Servers\AddServerSshKeyToVcsJob;
use App\Jobs\Servers\CreateServerSshKeyJob;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
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
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StoreServerAction
{
    public function execute(NewServerData $data, User $user): Server
    {
        return DB::transaction(function () use ($data, $user) {
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
                new CreateServerSshKeyJob($server),

                // TODO: CRITICAL! CONTINUE - test this.
                new UploadServerSshKeyToServerJob($server),

                // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

            ];

            if ($data->addSshKeyToVcs) {
                foreach ($user->vcsProviders as $vcsProvider)
                    $jobs[] = new AddServerSshKeyToVcsJob($server, $vcsProvider);
            }

            $configurationJobClass = (string) config('servers.types.' . $server->type . '.configuration_job_class');

            if (empty($configurationJobClass))
                throw new RuntimeException('Job class for this server type is not configured.');

            $jobs[] = new $configurationJobClass($server, $data);

            Bus::chain($jobs)->dispatch();

            return $server;
        }, 5);
    }
}
