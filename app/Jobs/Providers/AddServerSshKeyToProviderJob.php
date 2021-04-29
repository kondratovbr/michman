<?php declare(strict_types=1);

namespace App\Jobs\Providers;

use App\Models\Provider;
use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class AddServerSshKeyToProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;
    protected bool $force;

    /**
     * @param bool $force Send the key even if it is marked as already added to the provider.
     */
    public function __construct(Server $server, bool $force = false)
    {
        $this->onQueue('providers');

        $this->server = $server->withoutRelations();
        $this->force = $force;
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

            $addedKey = $api->addSshKeySafely($sshKey->name, $sshKey->publicKey);

            $sshKey->externalId = $addedKey->id;
            $sshKey->save();
        }, 5);
    }
}
