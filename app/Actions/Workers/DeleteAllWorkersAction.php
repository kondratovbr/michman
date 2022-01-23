<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\Jobs\Workers\DeleteWorkerJob;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class DeleteAllWorkersAction
{
    public function __construct(
        private DeleteWorkerAction $deleteWorker
    ) {}

    /** @return Collection<int, DeleteWorkerJob>|null */
    public function execute(Project $project, bool $returnJobs = false): Collection|null
    {
        $jobs = $project->workers->map(fn(Worker $worker) => $this->deleteWorker->execute($worker, true));

        if ($returnJobs)
            return $jobs;

        Bus::chain($jobs)->dispatch();

        return null;
    }
}
