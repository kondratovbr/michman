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

// TODO: CRITICAL! Figure out how to fail gracefully fail here if we can't get the commit hash and how to communicate this to the user.

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
                    /*
                     * TODO: CRITICAL! CONTINUE. Check the notifications get triggered and get stored in the DB.
                     *       Cover the email part with some placeholders, verify that it works and then figure out how to show completely different
                     *       stuff (different Blade templates somehow) based on the notification type on the front-end and implement the front-end part.
                     */
                    DeploymentFailedEvent::dispatch($deployment);
                })
                ->dispatch();

            return $deployment;
        }, 5);
    }
}
