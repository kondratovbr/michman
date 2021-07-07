<?php declare(strict_types=1);

namespace App\Events\Servers;

use App\Broadcasting\ServersChannel;
use App\Events\AbstractEvent;
use App\Models\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class AbstractServerEvent extends AbstractEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * https://laravel.com/docs/8.x/broadcasting#broadcasting-and-database-transactions
     */
    public $afterCommit = true;

    public int $serverKey;

    public function __construct(Server $server)
    {
        $this->serverKey = $server->getKey();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        return ServersChannel::channelInstance($this->serverKey);
    }
}
