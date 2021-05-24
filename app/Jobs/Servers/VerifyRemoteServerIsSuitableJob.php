<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Models\Server;
use App\Scripts\Root\VerifyServerIsSuitableScript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class VerifyRemoteServerIsSuitableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int The amount of seconds to wait between retries if a server isn't accessible yet. */
    protected const SECONDS_BETWEEN_RETRIES = 60;

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
    public function handle(VerifyServerIsSuitableScript $verifyServerIsSuitable): void
    {
        DB::transaction(function () use ($verifyServerIsSuitable) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            try {
                $ssh = $server->sftp('root');
            } catch (SshAuthFailedException $e) {
                $server->suitable = false;
                $server->save();
                return;
            }

            if (! $ssh->isConnected()) {
                $this->release(static::SECONDS_BETWEEN_RETRIES);
                return;
            }

            $server->suitable = $verifyServerIsSuitable->execute($server, $ssh);
            $server->save();
        }, 5);
    }
}
