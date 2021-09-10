<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\DataTransferObjects\NewServerDto;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithServerProviders;
use App\Models\Server;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestNewServerFromProviderJob extends AbstractJob
{
    use InteractsWithServerProviders;

    protected Server $server;
    protected NewServerDto $serverData;

    public function __construct(Server $server, NewServerDto $serverData)
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

            $server = $this->server->freshLockForUpdate();

            if (isset($server->externalId)) {
                Log::warning('RequestNewServerFromProviderJob: The server already has an external_id set. Server ID: ' . $server->getKey());
                return;
            }

            $api = $server->provider->api();

            $createdServer = $api->createServer($this->serverData, $server->workerSshKey->externalId);

            $server->externalId = $createdServer->id;
            $server->save();
        });
    }
}
