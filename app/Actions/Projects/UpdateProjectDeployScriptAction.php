<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectDeployScriptOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class UpdateProjectDeployScriptAction
{
    public function execute(Project $project, string $script): void
    {
        DB::transaction(function () use ($project, $script) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->deployScript = $script;
            $project->save();

            $jobs = $project->servers->map(
                fn(Server $server) => new UpdateProjectDeployScriptOnServerJob($server, $project)
            );

            Bus::batch($jobs)->onQueue($jobs->first()->queue)->dispatch();
        }, 5);
    }
}
