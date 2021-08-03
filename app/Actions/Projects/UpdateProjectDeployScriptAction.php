<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectDeployScriptOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
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

            Bus::batch($project->servers->map(
                fn(Server $server) => new UpdateProjectDeployScriptOnServerJob($server, $project)
            ))->dispatch();
        }, 5);
    }
}
