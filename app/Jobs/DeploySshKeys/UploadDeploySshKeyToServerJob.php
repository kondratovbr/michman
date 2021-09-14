<?php declare(strict_types=1);

namespace App\Jobs\DeploySshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Notifications\Servers\FailedToUploadServerSshKeyToServerNotification;
use App\Scripts\Root\UploadSshKeyToServerScript;
use Illuminate\Support\Facades\DB;
use Throwable;

class UploadDeploySshKeyToServerJob extends AbstractRemoteServerJob
{
    protected Project $project;

    public function __construct(Server $server, Project $project)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    public function handle(UploadSshKeyToServerScript $uploadSshKey): void
    {
        DB::transaction(function () use ($uploadSshKey) {
            $server = $this->server->freshSharedLock();
            $project = $this->project->freshSharedLock();

            $uploadSshKey->execute(
                $server,
                $project->deploySshKey,
                $project->serverUsername,
                $project->deploySshKey->name,
            );
        }, 5);
    }

    public function failed(Throwable $exception): void
    {
        $this->server->user->notify(new FailedToUploadServerSshKeyToServerNotification($this->server));
    }
}
