<?php declare(strict_types=1);

namespace App\Jobs\Providers;

use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AddServerSshKeyToProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('providers');

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
