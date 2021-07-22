<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerProviderJob;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
use App\Jobs\ServerSshKeys\CreateServerSshKeyJob;
use App\Jobs\WorkerSshKeys\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\UserSshKeys\UpdateUserSshKeysOnServerJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Jobs\Servers\VerifyRemoteServerIsSuitableJob;
use App\Jobs\Servers\UpdateServerAvailabilityJob;
use App\Models\Server;
use App\Models\User;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! Check and update tests!

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
                new UploadServerSshKeyToServerJob($server),

                // TODO: CRITICAL! Don't forget the rest of the stuff I should do here!

            ];
            
            $configurationJobClass = (string) config('servers.types.' . $server->type . '.configuration_job_class');

            if (empty($configurationJobClass))
                throw new RuntimeException('Configuration job class for this server type is not configured.');

            $jobs[] = new $configurationJobClass($server, $data);

            Bus::chain($jobs)->dispatch();

            return $server;
        }, 5);
    }
}
