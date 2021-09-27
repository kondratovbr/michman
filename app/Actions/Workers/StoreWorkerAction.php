<?php declare(strict_types=1);

namespace App\Actions\Workers;

use App\DataTransferObjects\WorkerDto;
use App\Jobs\Workers\StartWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class StoreWorkerAction
{
    public function execute(WorkerDto $data, Project $project, Server $server): Worker
    {
        return DB::transaction(function () use ($data, $project, $server): Worker {
            /** @var Worker $worker */
            $worker = $project->workers()->make($data->toArray());

            $worker->server()->associate($server);
            $worker->status = Worker::STATUS_STARTING;
            $worker->save();

            StartWorkerJob::dispatch($worker);

            return $worker;
        }, 5);
    }
}
