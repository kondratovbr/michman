<?php declare(strict_types=1);

namespace App\Actions\Servers;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\DataTransferObjects\NewServerDto;
use App\Jobs\UserSshKeys\UploadUserSshKeyToServerJob;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerProviderJob;
use App\Jobs\ServerSshKeys\CreateServerSshKeyJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Jobs\Servers\VerifyRemoteServerIsSuitableJob;
use App\Jobs\Servers\UpdateServerAvailabilityJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Notifications\Servers\FailedToConfigureServerNotification;
use App\States\Servers\Failed;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class StoreServerAction
{
    public function __construct(
        protected CreateWorkerSshKeyAction $createWorkerSshKey,
    ) {}

    public function execute(NewServerDto $data, Provider $provider): Server
    {
        return DB::transaction(function () use ($data, $provider): Server {
            $user = $provider->user;

            $attributes = $data->toArray([
                'ssh_port' => (string) config('servers.default_ssh_port'),
                'sudo_password' => Str::random(32),
            ]);

            if (! empty($data->database) && $data->database != 'none')
                $attributes['database_root_password'] = Str::random(32);

            /** @var Server $server */
            $server = $provider->servers()->create($attributes);

            $this->createWorkerSshKey->execute($server);

            $server->userSshKeys()->sync($user->userSshKeys);

            $jobs = [
                new AddWorkerSshKeyToServerProviderJob($server),
                new RequestNewServerFromProviderJob($server, $data),
                new GetServerPublicIpJob($server),
                new VerifyRemoteServerIsSuitableJob($server),
                new PrepareRemoteServerJob($server),
                new UpdateServerAvailabilityJob($server),
                new AddWorkerSshKeyToServerJob($server),
                new CreateServerSshKeyJob($server),
                new UploadServerSshKeyToServerJob($server, (string) config('servers.worker_user')),
            ];

            /** @var UserSshKey $key */
            foreach ($server->userSshKeys as $key) {
                $jobs[] = new UploadUserSshKeyToServerJob($key, $server);
            }

            $configurationJobClass = (string) config("servers.types.{$server->type}.configuration_job_class");

            if (empty($configurationJobClass))
                throw new RuntimeException('Configuration job class for this server type is not configured.');

            $jobs[] = new $configurationJobClass($server, $data);

            Bus::chain($jobs)->catch(function (Throwable $exception) use ($user, $server) {
                $server->refresh();
                $server->state->transitionTo(Failed::class);
                $user->notify(new FailedToConfigureServerNotification($server));
            })->dispatch();

            return $server;
        }, 5);
    }
}
