<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateProjectEnvironmentOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class UpdateProjectEnvironmentAction
{
    public function execute(Project $project, string $environment): void
    {
        DB::transaction(function () use ($project, $environment) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->environment = $environment;
            $project->save();

            Bus::batch($project->servers->map(
                fn(Server $server) => new UpdateProjectEnvironmentOnServerJob($server, $project)
            ))->dispatch();
        }, 5);
    }
}
