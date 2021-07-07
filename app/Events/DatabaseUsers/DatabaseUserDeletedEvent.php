<?php declare(strict_types=1);

namespace App\Events\DatabaseUsers;

use App\Events\Servers\AbstractServerEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseUserDeletedEvent extends AbstractDatabaseUserEvent implements ShouldBroadcast
{
    //
}
