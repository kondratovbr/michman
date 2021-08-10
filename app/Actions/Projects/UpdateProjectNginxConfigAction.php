<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectNginxConfigOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class UpdateProjectNginxConfigAction
{
    public function execute(Project $project, string $nginxConfig): void
    {
        DB::transaction(function () use ($project, $nginxConfig) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->nginxConfig = $nginxConfig;
            $project->save();

            $jobs = $project->servers->map(
                fn(Server $server) => new UpdateProjectNginxConfigOnServerJob($server, $project)
            );

            Bus::batch($jobs)->onQueue($jobs->first()->queue)->dispatch();
        }, 5);
    }
}
