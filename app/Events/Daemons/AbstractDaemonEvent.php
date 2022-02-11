<?php declare(strict_types=1);

namespace App\Events\Daemons;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Daemon;
use Illuminate\Broadcasting\Channel;

abstract class AbstractDaemonEvent extends AbstractEvent
{
    use Broadcasted;

    protected int $daemonKey;
    protected int $serverKey;

    public function __construct(Daemon $daemon)
    {
        $this->daemonKey = $daemon->getKey();
        $this->serverKey = $daemon->serverId;
    }

    protected function getChannels(): Channel
    {
        return ServerChannel::channelInstance($this->serverKey);
    }
}
