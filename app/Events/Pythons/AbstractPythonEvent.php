<?php declare(strict_types=1);

namespace App\Events\Pythons;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Python;
use Illuminate\Broadcasting\Channel;

abstract class AbstractPythonEvent extends AbstractEvent
{
    use Broadcasted;

    public int $pythonKey;
    public int $serverKey;

    public function __construct(Python $python)
    {
        $this->pythonKey = $python->getKey();
        $this->serverKey = $python->serverId;
    }

    protected function getChannels(): Channel
    {
        return ServerChannel::channelInstance($this->serverKey);
    }
}
