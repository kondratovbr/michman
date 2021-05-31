<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Scripts\Root\VerifyServerAvailabilityScript;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;
use Throwable;

class UpdateServerAvailabilityJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(5);
    }

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('servers');
        $this->timeout = 60; // 1 min
        $this->backoff = 10; // 10 sec

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(VerifyServerAvailabilityScript $verifyServerAvailability): void
    {
        DB::transaction(function () use ($verifyServerAvailability) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // We will remove the availability status of the server before starting the checking process
            // in case it wasn't done immediately, so we could show the progress to user.
            if (! is_null($server->available)) {
                $server->available = null;
                $server->save();
                // This allow us to run the job doing only one transaction.
                $this->release();
                return;
            }

            try {
                $ssh = $server->sftp();
            } catch (SshAuthFailedException $exception) {
                $server->available = false;
                $server->save();
                return;
            }

            if (! $ssh->isConnected()) {
                $this->release($this->backoff);
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
