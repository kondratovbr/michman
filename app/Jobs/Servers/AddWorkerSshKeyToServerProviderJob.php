<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Support\Facades\DB;

class AddWorkerSshKeyToServerProviderJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('providers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            /** @var WorkerSshKey $sshKey */
            $sshKey = $server->workerSshKey()
                ->lockForUpdate()
                ->firstOrFail();

            $api = $this->server->provider->api();

            $addedKey = $api->addSshKeySafely($sshKey->name, $sshKey->publicKeyString);

            $sshKey->externalId = $addedKey->id;
            $sshKey->save();
        }, 5);
    }
}
