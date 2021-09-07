<?php declare(strict_types=1);

namespace App\Events\Daemons;

use App\Events\Servers\AbstractServerEvent;
use App\Models\Daemon;

abstract class AbstractDaemonEvent extends AbstractServerEvent
{
    public int $daemonKey;

    public function __construct(Daemon $daemon)
    {
        parent::__construct($daemon->server);

        $this->daemonKey = $daemon->getKey();
    }
}
