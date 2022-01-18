<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;

// TODO: CRITICAL! DELETING. Implement.

class UninstallProjectFromServerJob extends AbstractRemoteServerJob
{
    protected Project $project;

    public function __construct(Project $project, Server $server)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    public function handle(): void
    {
        //
    }
}
