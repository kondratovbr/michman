<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Models\Worker;

// TODO: CRITICAL! CONTINUE. Implement and cover with tests.

class RetrieveWorkerLogAction
{
    public function __construct(
        //
    ) {}

    public function execute(Worker $worker): string|false
    {
        return 'Worker log, mothefucka!';

        //
    }
}
