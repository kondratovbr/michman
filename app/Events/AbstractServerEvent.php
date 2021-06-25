<?php declare(strict_types=1);

namespace App\Events;

use App\Models\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class AbstractServerEvent extends AbstractEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * https://laravel.com/docs/8.x/broadcasting#broadcasting-and-database-transactions
     */
    public $afterCommit = true;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server->withoutRelations();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        return new PrivateChannel('servers.' . $this->server->getKey());
    }
}
