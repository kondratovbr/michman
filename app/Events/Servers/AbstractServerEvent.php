<?php declare(strict_types=1);

namespace App\Events\Servers;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Models\Server;
use Illuminate\Broadcasting\Channel;

abstract class AbstractServerEvent extends AbstractEvent
{
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
        return ServerChannel::channelInstance($this->serverKey);
    }
}
