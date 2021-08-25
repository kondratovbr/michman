<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectEnvironmentOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
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

            $jobs = $project->servers->map(
                fn(Server $server) => new UpdateProjectEnvironmentOnServerJob($server, $project)
            );

            Bus::batch($jobs)->onQueue($jobs->first()->queue)->dispatch();
        }, 5);
    }
}
