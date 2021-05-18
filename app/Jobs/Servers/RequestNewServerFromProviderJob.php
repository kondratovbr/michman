<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\DataTransferObjects\NewServerData;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

// TODO: Should I refactor all provider-interacting and server-interacting jobs to keep throttling and retries DRY?

class RequestNewServerFromProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;
    protected NewServerData $serverData;

    public function __construct(Server $server, NewServerData $serverData)
    {
        $this->onQueue('providers');

        $this->server = $server->withoutRelations();
        $this->serverData = $serverData;
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [
            (new ThrottlesExceptions(3, 10))->backoff(1),
        ];
    }

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(30);
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
