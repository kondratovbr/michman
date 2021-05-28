<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\DataTransferObjects\NewServerData;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithServerProviders;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class RequestNewServerFromProviderJob extends AbstractJob
{
    use InteractsWithServerProviders;

    protected Server $server;
    protected NewServerData $serverData;

    public function __construct(Server $server, NewServerData $serverData)
    {
        $this->setQueue('providers');

        $this->server = $server->withoutRelations();
        $this->serverData = $serverData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {

            // TODO: IMPORTANT! Make sure to handle a situation when another server with the same name already exists on this provider.

            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (isset($server->externalId))
                throw new \RuntimeException('The server already has an external_id set.');

            $api = $server->provider->api();

            $createdServer = $api->createServer($this->serverData, $server->workerSshKey->externalId);

            $server->externalId = $createdServer->id;
            $server->save();
        });
    }
}
