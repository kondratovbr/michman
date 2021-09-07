<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\DataTransferObjects\WorkerData;
use App\Jobs\Workers\StartWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

// TODO: CRITICAL! Don't forget to implement the job.

class StoreWorkerAction
{
    public function execute(WorkerData $data, Project $project, Server $server): Worker
    {
        return DB::transaction(function () use ($data, $project, $server): Worker {
            /** @var Worker $worker */
            $worker = $project->workers()->make($data->toArray());

            $worker->status = Worker::STATUS_STARTING;

            $worker->server()->associate($server);

            $worker->save();

            StartWorkerJob::dispatch($worker);

            return $worker;
        }, 5);
    }
}
