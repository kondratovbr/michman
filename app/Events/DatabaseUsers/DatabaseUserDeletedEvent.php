<?php declare(strict_types=1);

namespace App\Events\DatabaseUsers;

use App\Events\AbstractServerEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseUserDeletedEvent extends AbstractServerEvent implements ShouldBroadcast
{
    //
}
