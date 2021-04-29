<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class GetServerPublicIpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;

    /** @var int The amount of seconds to wait between retries if an address wasn't issued yet. */
    protected const SECONDS_BETWEEN_RETRIES = 30;

    public function __construct(Server $server)
    {
        $this->onQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [
            (new ThrottlesExceptions(3, 10))->backoff(1)
        ];
    }

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(60);
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

            $ip = $server->provider->api()->getServerPublicIp4($server->externalId);

            if (is_null($ip)) {
                $this->release(static::SECONDS_BETWEEN_RETRIES);
                return;
            }

            $server->ip = $ip;
            $server->save();
        }, 5);
    }
}
