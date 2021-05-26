<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Models\Server;
use App\Scripts\Root\VerifyServerAvailabilityScript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;
use Throwable;

class UpdateServerAvailabilityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int The amount of seconds to wait between retries if a server isn't accessible yet. */
    protected const SECONDS_BETWEEN_RETRIES = 10;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->onQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [
            (new ThrottlesExceptions(3, 1))->backoff(1),
        ];
    }

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(5);
    }

    /**
     * Execute the job.
     */
    public function handle(VerifyServerAvailabilityScript $verifyServerAvailability): void
    {
        // We want to remove the availability status of the server before starting the checking process,
        // so we could show the progress to the user.
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! is_null($server->available)) {
                $server->available = null;
                $server->save();
            }
        }, 5);

        DB::transaction(function () use ($verifyServerAvailability) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            try {
                $ssh = $server->sftp();
            } catch (SshAuthFailedException $exception) {
                $server->available = false;
                $server->save();
                return;
            }

            if (! $ssh->isConnected()) {
                $this->release(static::SECONDS_BETWEEN_RETRIES);
                return;
            }

            $server->available = $verifyServerAvailability->execute($server, $ssh);
            $server->save();
        }, 5);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $server->available = false;
            $server->save();
        }, 5);
    }
}
