<?php declare(strict_types=1);

namespace App\Actions\Deployments;

use App\DataTransferObjects\DeploymentDto;
use App\Events\Deployments\DeploymentFinishedEvent;
use App\Events\Deployments\DeploymentFailedEvent;
use App\Jobs\Deployments\PerformDeploymentOnServerJob;
use App\Models\Deployment;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: An automatic rollback on failure feature would be nice to have.

// TODO: IMPORTANT! Cover with tests!

class StoreDeploymentAction
{
    public function execute(DeploymentDto $data, Project $project): Deployment
    {
        return DB::transaction(function () use ($data, $project): Deployment {
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($project->getKey());

            /** @var Deployment $deployment */
            $deployment = $project->deployments()->create($data->toArray());

            $deployment->servers()->sync($project->servers);

            $jobs = $deployment->servers->map(
                fn(Server $server) => new PerformDeploymentOnServerJob($deployment, $server)
            );

            Bus::batch($jobs)
                ->onQueue($jobs->first()->queue)
                ->allowFailures()
                ->then(function (Batch $batch) use ($deployment) {
                    DeploymentFinishedEvent::dispatch($deployment);
                })
                ->catch(function (Batch $batch) use ($deployment) {
                    DeploymentFailedEvent::dispatch($deployment);
                })
                ->dispatch();

            return $deployment;
        }, 5);
    }
}
