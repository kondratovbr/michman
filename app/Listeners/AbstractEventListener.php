<?php declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;

abstract class AbstractEventListener
{
    use InteractsWithQueue;

    /**
     * https://laravel.com/docs/8.x/events#queued-event-listeners-and-database-transactions
     */
    public bool $afterCommit = true;

    /** The name of the queue the job should be sent to. */
    public string $queue = 'default';

    /** The time (seconds) before the job should be processed. */
    public int $delay = 0;
}
