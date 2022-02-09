<?php declare(strict_types=1);

namespace App\Events\Servers;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerDeletedEvent extends AbstractServerEvent implements ShouldBroadcast
{
    //
}
