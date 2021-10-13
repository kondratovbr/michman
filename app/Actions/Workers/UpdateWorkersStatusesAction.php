<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\UpdateWorkerStateJob;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Support\Facades\Bus;

class UpdateWorkersStatusesAction
{
    public function execute(Project $project): void
    {
        $jobs = $project->workers->map(
            fn(Worker $worker) => new UpdateWorkerStateJob($worker)
        );

        Bus::batch($jobs)
            ->onQueue($jobs->first()->queue)
            ->allowFailures()
            ->dispatch();
    }
}
