<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectEnvironmentOnAllServersJob;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class UpdateProjectEnvironmentAction
{
    public function execute(Project $project, string $environment): void
    {
        DB::transaction(function () use ($project, $environment) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->environment = $environment;
            $project->save();

            UpdateProjectEnvironmentOnAllServersJob::dispatch($project);
        }, 5);
    }
}
