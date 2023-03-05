<?php declare(strict_types=1);

namespace App\Events;

use App\Events\Traits\Snaggable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class AbstractEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use Snaggable;

    /**
     * https://laravel.com/docs/8.x/broadcasting#broadcasting-and-database-transactions
     */
    public bool $afterCommit = true;

    /** The name of the queue on which to place the broadcasting job. */
    public string $queue = 'broadcasting';

    /** The number of times the queued listener may be attempted. */
    public int $tries = 5;
}
