<?php declare(strict_types=1);

namespace App\Jobs\WorkerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithServerProviders;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Notifications\Providers\AddingSshKeyToProviderFailedNotification;
use Illuminate\Support\Facades\DB;
use Throwable;

// TODO: CRITICAL! Cover with tests.

class AddWorkerSshKeyToServerProviderJob extends AbstractJob
{
    use InteractsWithServerProviders;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('providers');

        $this->server = $server->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $server = $this->server->freshSharedLock();
            /** @var WorkerSshKey $sshKey */
            $sshKey = $server->workerSshKey()->lockForUpdate()->firstOrFail();

            $api = $this->server->provider->api();

            $addedKey = $api->addSshKeySafely(
                $sshKey->name,
                $sshKey->getPublicKeyString(false),
            );

            $sshKey->externalId = $addedKey->id;
            $sshKey->save();
        }, 5);
    }

    public function failed(Throwable $exception): void
    {
        $this->server->user->notify(new AddingSshKeyToProviderFailedNotification($this->server->provider));
    }
}
