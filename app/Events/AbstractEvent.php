<?php declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class AbstractEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * https://laravel.com/docs/8.x/broadcasting#broadcasting-and-database-transactions
     */
    public $afterCommit = true;
    
    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'broadcasting';
}
