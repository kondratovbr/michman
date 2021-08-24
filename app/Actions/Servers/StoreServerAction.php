<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\DataTransferObjects\NewServerData;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerProviderJob;
use App\Jobs\ServerSshKeys\CreateServerSshKeyJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\UserSshKeys\UpdateWorkerSshKeysOnServerJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
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
    public function __construct(
        protected CreateWorkerSshKeyAction $createWorkerSshKey,
    ) {}

    public function execute(NewServerData $data, User $user): Server
    {
        return DB::transaction(function () use ($data, $user) {
            $attributes = $data->toArray();
            $attributes['ssh_port'] = (string) config('servers.default_ssh_port');
            $attributes['sudo_password'] = Str::random(32);

            /** @var Server $server */
            $server = $data->provider->servers()->create($attributes);

            $this->createWorkerSshKey->execute($server);

            $jobs = [
                new AddWorkerSshKeyToServerProviderJob($server),
                new RequestNewServerFromProviderJob($server, $data),
                new GetServerPublicIpJob($server),
                new VerifyRemoteServerIsSuitableJob($server),
                new PrepareRemoteServerJob($server),
                new UpdateServerAvailabilityJob($server),
                new UpdateWorkerSshKeysOnServerJob($server),
                new CreateServerSshKeyJob($server),
                new UploadServerSshKeyToServerJob($server, (string) config('servers.worker_user')),
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
