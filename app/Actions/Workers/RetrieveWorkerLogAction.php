<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Models\Worker;
use App\Scripts\Root\RetrieveWorkerLogScript;
use Throwable;

class RetrieveWorkerLogAction
{
    public function __construct(
        protected RetrieveWorkerLogScript $script,
    ) {}

    public function execute(Worker $worker): string|false
    {
        try {
            return retry(
                5,
                fn() => $this->script->execute($worker->server, $worker),
                100,
            );
        } catch (Throwable) {
            return false;
        }
    }
}
