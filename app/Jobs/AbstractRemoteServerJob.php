<?php declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Middleware\WithoutOverlappingOnModel;
use App\Models\Server;
use DateTimeInterface;
use Illuminate\Queue\Middleware\ThrottlesExceptions;

abstract class AbstractRemoteServerJob extends AbstractJob
{
    protected Server $server;

    /** The number of seconds the job can run before timing out. */
    public int $timeout = 60 * 15; // 15 min

    /**
     * The number of seconds to wait before retrying the job.
     *
     * If null then the job will use ThrottlesException middleware.
     */
    public int|null $backoff = null;

    /** If a job isn't completed until this time - it will be failed. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(60);
    }

    /** @return $this */
    public function __construct(Server $server, bool $sync = false)
    {
        parent::__construct();

        $this->sync($sync);

        $this->server = $server->withoutRelations();

        return $this;
    }

    protected function getQueue(): string
    {
        return 'servers';
    }

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        if ($this->sync)
            return [];

        $middleware = [];

        if (is_null($this->backoff ?? null)) {
            $middleware[] = (new ThrottlesExceptions(5, 5))->backoff(1);
        }

        $middleware[] = (new WithoutOverlappingOnModel($this->server))
            // If another job already works with the same server - retry this one 30 seconds later.
            ->releaseAfter(30)
            // In case a job with a lock crashes the worker
            // - the lock will be automatically released after some time,
            // specifically - one minute after the job itself should have timed out.
            ->expireAfter($this->timeout + 60);

        return $middleware;
    }
}
