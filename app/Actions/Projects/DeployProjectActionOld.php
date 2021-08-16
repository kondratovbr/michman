<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Deployments\PerformDeploymentJob;
use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class DeployProjectActionOld
{
    public function execute(Project $project): void
    {
        DB::transaction(function () use ($project) {
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($project->getKey());

            /** @var Deployment $deployment */
            $deployment = $project->deployments()->create([
                'branch' => $project->branch,
            ]);

            $deployment->servers()->sync($project->servers);

            PerformDeploymentJob::dispatch($deployment);
        }, 5);
    }
}
