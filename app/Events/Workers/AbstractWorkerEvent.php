<?php declare(strict_types=1);

namespace App\Events\Workers;

use App\Events\Servers\AbstractServerEvent;
use App\Models\Worker;

abstract class AbstractWorkerEvent extends AbstractServerEvent
{
    public int $workerKey;

    public function __construct(Worker $worker)
    {
        parent::__construct($worker->server);

        $this->workerKey = $worker->getKey();
    }
}
