<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class SetupProjectJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Project $project;
    protected Server $server;

    public function __construct(Project $project, Server $server)
    {
        $this->setQueue('servers');

        $this->project = $project->withoutRelations();
        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Server $server */
            $server = Server::query()->lockForUpdate()->findOrFail($this->server->getKey());
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($this->project->getKey());

            //
        }, 5);
    }
}
