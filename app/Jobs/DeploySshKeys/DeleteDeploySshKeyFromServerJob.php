<?php declare(strict_types=1);

namespace App\Jobs\DeploySshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;

class DeleteDeploySshKeyFromServerJob extends AbstractRemoteServerJob
{
    protected Project $project;

    public function __construct(Server $server, Project $project)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    public function handle(): void
    {
        // TODO: CRITICAL! CONTINUE. Implement.

        //
    }
}
