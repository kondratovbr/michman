<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Events\Deployments\DeploymentCompletedEvent;
use App\Events\Deployments\DeploymentFailedEvent;
use App\Jobs\Deployments\PerformDeploymentOnServerJob;
use App\Models\Deployment;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Figure out how to fail gracefully here if we can't get the commit hash and how to communicate this to the user.

// TODO: CRITICAL! Should I enable the placeholder back if the deployment fails? I mean, when a project has been deployed already, what to do if a new deployment fails?

// TODO: An automatic rollback on failure feature would be nice to have.

// TODO: CRITICAL! Cover with tests!

class DeployProjectAction
{
    public function execute(Project $project): Deployment
    {
        return DB::transaction(function () use ($project): Deployment {
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($project->getKey());

            /** @var Deployment $deployment */
            $deployment = $project->deployments()->create([
                'branch' => $project->branch,
                'commit' => $project->vcsProvider->api()
                    ->getLatestCommitHash($project->repo, $project->branch),
                'environment' => $project->environment,
                'deploy_script' => $project->deployScript,
                'gunicorn_config' => $project->gunicornConfig,
                'nginx_config' => $project->nginxConfig,
            ]);

            $deployment->servers()->sync($project->servers);

            $jobs = $deployment->servers->map(
                fn(Server $server) => new PerformDeploymentOnServerJob($deployment, $server)
            );

            Bus::batch($jobs)
                ->onQueue($jobs->first()->queue)
                ->allowFailures()
                ->then(function (Batch $batch) use ($deployment) {
                    DeploymentCompletedEvent::dispatch($deployment);
                })
                ->catch(function (Batch $batch) use ($deployment) {
                    DeploymentFailedEvent::dispatch($deployment);
                })
                ->dispatch();

            return $deployment;
        }, 5);
    }
}
