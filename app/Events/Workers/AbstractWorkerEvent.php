<?php declare(strict_types=1);

namespace App\Events\Workers;

use App\Events\Projects\AbstractProjectEvent;
use App\Models\Worker;

abstract class AbstractWorkerEvent extends AbstractProjectEvent
{
    public int $workerKey;

    public function __construct(Worker $worker)
    {
        parent::__construct($worker->project);

        $this->workerKey = $worker->getKey();
    }
}
