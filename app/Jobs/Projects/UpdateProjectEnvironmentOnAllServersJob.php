<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Project;
use App\Scripts\User\UpdateProjectEnvironmentOnServerScript;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProjectEnvironmentOnAllServersJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Project $project;

    public function __construct(Project $project)
    {
        $this->setQueue('servers');

        $this->project = $project->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(UpdateProjectEnvironmentOnServerScript $script): void
    {
        DB::transaction(function () use ($script) {
            /** @var Project $project */
            $project = Project::query()
                ->with('servers')
                ->lockForUpdate()
                ->findOrFail($this->project->getKey());

            if ($project->servers->count() == 0) {
                Log::warning('UpdateProjectEnvironmentOnAllServersJob run for a project that has no servers.');
                return;
            }

            foreach ($project->servers as $server) {
                $script->execute($server, $project);
            }
        }, 5);
    }
}