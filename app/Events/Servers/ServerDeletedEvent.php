<?php declare(strict_types=1);

namespace App\Events\Servers;

use App\Broadcasting\ServerChannel;
use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Models\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerDeletedEvent extends AbstractEvent implements ShouldBroadcast
{
    protected int $serverKey;
    protected int $userKey;

    public function __construct(Server $server)
    {
        $this->serverKey = $server->getKey();
        $this->userKey = $server->user->getKey();
    }

    public function broadcastOn(): Channel|array
    {
        return [
            ServerChannel::channelInstance($this->serverKey),
            UserChannel::channelInstance($this->userKey),
        ];
    }
}
