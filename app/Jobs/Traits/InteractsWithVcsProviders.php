<?php declare(strict_types=1);

namespace App\Jobs\Traits;

use DateTimeInterface;

trait InteractsWithVcsProviders
{
    /** The number of seconds the job can run before timing out. */
    public int $timeout = 60 * 5; // 5 min

    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 60; // 1 min

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(30);
    }

    protected function getQueue(): string
    {
        return 'providers';
    }
}
