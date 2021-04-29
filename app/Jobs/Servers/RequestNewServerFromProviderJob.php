<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\DataTransferObjects\NewServerData;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/*
 * TODO: CRITICAL! This job (and others interacting with third-party APIs) should be:
 *       1. Rate-limited
 *       2. Able to "backoff" when the API is unresponsive
 *       Laravel has built-in features for this - see docs.
 */

class RequestNewServerFromProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;
    protected NewServerData $serverData;

    public function __construct(Server $server, NewServerData $serverData)
    {
        $this->onQueue('provider');

        $this->server = $server->withoutRelations();
        $this->serverData = $serverData;
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

            $api = $server->provider->api();

            $createdServer = $api->createServer($this->serverData, $server->workerSshKey->externalId);

            $server->externalId = $createdServer->id;
            $server->save();
        });
    }
}
