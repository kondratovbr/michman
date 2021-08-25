<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\User\UpdateProjectEnvironmentOnServerScript;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProjectEnvironmentOnServerJob extends AbstractRemoteServerJob
{
    use Batchable;

    /** @var bool Delete the job if its models no longer exist. */
    public $deleteWhenMissingModels = true;

    protected Project $project;

    public function __construct(Server $server, Project $project)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(UpdateProjectEnvironmentOnServerScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->lockServer();

            if (! $server->projects->contains($this->project)) {
                Log::warning('UpdateProjectEnvironmentOnServerJob: The project is no longer deployed on this server.');
                return;
            }

            $script->execute($server, $this->project);
        }, 5);
    }
}
