<?php declare(strict_types=1);

namespace App\Events\Workers;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WorkerUpdatedEvent extends AbstractWorkerEvent implements ShouldBroadcast
{
    //
}
