<?php declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Middleware\WithoutOverlappingOnModel;
use App\Models\Server;
use DateTimeInterface;

abstract class AbstractRemoteServerJob extends AbstractJob
{
    protected Server $server;

    /** The number of seconds the job can run before timing out. */
    public int $timeout = 60 * 30; // 30 min

    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 60; // 1 min

    /**
     * If a job isn't completed until this time - it will be failed.
     */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(60);
    }

    public function __construct(Server $server)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlappingOnModel($this->server))
                // If another job already works with the same server - retry this one 1 minute later.
                ->releaseAfter(60)
                // In case a job with a lock crashes the worker
                // - the lock will be automatically released after some time,
                // specifically - one minute after the job itself should have timed out.
                ->expireAfter($this->timeout + 60),
        ];
    }

    /**
     * Reload the Server model from the DB and lock it for update.
     */
    protected function lockServer(): Server
    {
        $this->server = Server::query()->lockForUpdate()->findOrFail($this->server->getKey());

        return $this->server;
    }
}