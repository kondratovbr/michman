<?php declare(strict_types=1);

namespace App\Events\Databases;

use App\Events\AbstractServerEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseDeletedEvent extends AbstractServerEvent implements ShouldBroadcast
{
    //
}
