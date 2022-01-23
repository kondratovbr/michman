<?php declare(strict_types=1);

namespace App\Jobs\DeploySshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Root\DeleteSshKeyFromUserScript;
use Illuminate\Support\Facades\DB;

class DeleteDeploySshKeyFromServerJob extends AbstractRemoteServerJob
{
    protected Project $project;

    public function __construct(Server $server, Project $project)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    public function handle(DeleteSshKeyFromUserScript $deleteKey): void
    {
        DB::transaction(function () use ($deleteKey) {
            $server = $this->server->freshSharedLock();
            $project = $this->project->freshSharedLock();

            if (is_null($project->deploySshKey))
                return;

            $deleteKey->execute(
                $server,
                $project->serverUsername,
                $project->deploySshKey
            );
        }, 5);
    }
}
