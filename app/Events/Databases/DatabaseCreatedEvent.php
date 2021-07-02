<?php declare(strict_types=1);

namespace App\Events\Databases;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseCreatedEvent extends AbstractDatabaseEvent implements ShouldBroadcast
{
    //
}
