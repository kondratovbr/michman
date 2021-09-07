<?php declare(strict_types=1);

namespace App\Events\Daemons;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DaemonUpdatedEvent extends AbstractDaemonEvent implements ShouldBroadcast
{
    //
}
