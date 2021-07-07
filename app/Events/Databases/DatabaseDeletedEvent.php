<?php declare(strict_types=1);

namespace App\Events\Databases;

use App\Events\Servers\AbstractServerEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseDeletedEvent extends AbstractDatabaseEvent implements ShouldBroadcast
{
    //
}
