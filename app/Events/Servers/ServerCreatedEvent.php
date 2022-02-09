<?php declare(strict_types=1);

namespace App\Events\Servers;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerCreatedEvent extends AbstractServerEvent implements ShouldBroadcast
{
    //
}
