<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\User\UpdateProjectGunicornConfigOnServerScript;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// TODO: CRITICAL! Cover with tests.

class UpdateGunicornConfigOnServerJob extends AbstractRemoteServerJob
{
    use Batchable;

    protected Project $project;

    public function __construct(Server $server, Project $project)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(UpdateProjectGunicornConfigOnServerScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->lockServer();

            if (! $server->projects->contains($this->project)) {
                Log::warning('UpdateGunicornConfigOnServerJob: The project is no longer deployed on this server.');
                return;
            }

            $script->execute($server, $this->project);
        }, 5);
    }
}