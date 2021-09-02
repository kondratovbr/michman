<?php declare(strict_types=1);

namespace App\Events\Workers;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WorkerCreatedEvent extends AbstractWorkerEvent implements ShouldBroadcast
{
    //
}
