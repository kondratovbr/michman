<?php declare(strict_types=1);

namespace App\Jobs\DeploySshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Root\UploadSshKeyToServerScript;
use Illuminate\Support\Facades\DB;

class UploadDeploySshKeyToServerJob extends AbstractRemoteServerJob
{
    protected Project $project;

    public function __construct(Server $server, Project $project)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(UploadSshKeyToServerScript $uploadSshKey): void
    {
        DB::transaction(function () use ($uploadSshKey) {
            /** @var Server $server */
            $server = Server::query()->lockForUpdate()->findOrFail($this->server->getKey());
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($this->project->getKey());

            $uploadSshKey->execute(
                $server,
                $project->deploySshKey,
                $project->serverUsername,
                $project->deploySshKey->name,
            );
        }, 5);
    }
}
