<?php declare(strict_types=1);

namespace App\Events\DatabaseUsers;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseUserUpdatedEvent extends AbstractDatabaseUserEvent implements ShouldBroadcast
{
    //
}
