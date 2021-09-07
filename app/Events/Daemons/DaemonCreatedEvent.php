<?php declare(strict_types=1);

namespace App\Events\Daemons;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DaemonCreatedEvent extends AbstractDaemonEvent implements ShouldBroadcast
{
    //
}
