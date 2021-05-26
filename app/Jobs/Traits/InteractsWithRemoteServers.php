<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use Illuminate\Queue\Middleware\ThrottlesExceptions;
use DateTimeInterface;

trait InteractsWithRemoteServers
{
    /** The number of seconds the job can run before timing out. */
    public int $timeout = 60 * 30; // 30 min

    /** Get the middleware the job should pass through. */
    public function middleware(): array
    {
        return [
            // If the job encounters and exception 3 times in 15 minutes it will retry after 5 minutes.
            // In case the server is temporarily unavailable for some reason, like it's rebooting.
            (new ThrottlesExceptions(3, 15))->backoff(5),
        ];
    }

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(60);
    }
}
