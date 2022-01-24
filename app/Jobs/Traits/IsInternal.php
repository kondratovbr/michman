<?php declare(strict_types=1);

namespace App\Jobs\Traits;

trait IsInternal
{
    /** The number of times the job may be attempted. */
    public int $tries = 5;

    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 5;

    protected function getQueue(): string
    {
        return 'default';
    }
}
