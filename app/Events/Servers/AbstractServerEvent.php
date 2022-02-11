<?php declare(strict_types=1);

namespace App\Events\Servers;

use App\Broadcasting\ServerChannel;
use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Server;

abstract class AbstractServerEvent extends AbstractEvent
{
    use Broadcasted;

    public int $serverKey;
    public int|null $userKey;

    public function __construct(Server $server)
    {
        $this->serverKey = $server->getKey();
        $this->userKey = $server->provider?->userId;
    }

    protected function getChannels(): array
    {
        return [
            ServerChannel::channelInstance($this->serverKey),
            UserChannel::channelInstance($this->userKey),
        ];
    }
}
