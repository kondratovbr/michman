<?php declare(strict_types=1);

namespace App\Events\DatabaseUsers;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\DatabaseUser;
use Illuminate\Broadcasting\Channel;

abstract class AbstractDatabaseUserEvent extends AbstractEvent
{
    use Broadcasted;

    protected int $serverKey;

    public function __construct(DatabaseUser $databaseUser)
    {
        $this->serverKey = $databaseUser->serverId;
    }

    protected function getChannels(): Channel
    {
        return ServerChannel::channelInstance($this->serverKey);
    }
}
