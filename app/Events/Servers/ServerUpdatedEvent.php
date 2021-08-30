<?php declare(strict_types=1);

namespace App\Events\Servers;

use App\Events\Users\AbstractUserEvent;
use App\Models\Server;

class ServerUpdatedEvent extends AbstractUserEvent
{
    protected int $serverKey;

    public function __construct(Server $server)
    {
        parent::__construct($server->user);

        $this->serverKey = $server->getKey();
    }
}
