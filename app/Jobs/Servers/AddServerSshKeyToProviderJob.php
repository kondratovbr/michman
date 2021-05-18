<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class AddServerSshKeyToProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('providers');

        $this->server = $server->withoutRelations();
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
