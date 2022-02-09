<?php declare(strict_types=1);

namespace App\Events\Databases;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Database;
use Illuminate\Broadcasting\Channel;

abstract class AbstractDatabaseEvent extends AbstractEvent
{
    use Broadcasted;

    protected int $serverKey;

    public function __construct(Database $database)
    {
        $this->serverKey = $database->serverId;
    }

    protected function getChannels(): Channel
    {
        return ServerChannel::channelInstance($this->serverKey);
    }
}
