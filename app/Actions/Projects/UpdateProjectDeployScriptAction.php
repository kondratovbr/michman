<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectDeployScriptOnAllServersJob;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class UpdateProjectDeployScriptAction
{
    public function execute(Project $project, string $script): void
    {
        DB::transaction(function () use ($project, $script) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->deployScript = $script;
            $project->save();

            UpdateProjectDeployScriptOnAllServersJob::dispatch($project);
        }, 5);
    }
}
