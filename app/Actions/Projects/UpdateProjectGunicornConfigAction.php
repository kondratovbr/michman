<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\UpdateGunicornConfigOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class UpdateProjectGunicornConfigAction
{
    public function execute(Project $project, string $config): void
    {
        DB::transaction(function () use ($project, $config) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->gunicornConfig = $config;
            $project->save();

            $jobs = $project->servers->map(
                fn(Server $server) => new UpdateGunicornConfigOnServerJob($server, $project)
            );

            Bus::batch($jobs)->onQueue($jobs->first()->queue)->dispatch();
        }, 5);
    }
}
