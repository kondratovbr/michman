<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Root\DeleteGunicornConfigScript;
use App\Scripts\Root\DeleteNginxProjectConfigScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\User\DeleteProjectFilesScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! CONTINUE. Test.

class UninstallProjectFromServerJob extends AbstractRemoteServerJob
{
    protected Project $project;

    public function __construct(Project $project, Server $server)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
    }

    public function handle(
        DeleteNginxProjectConfigScript $deleteNginxConfig,
        RestartNginxScript             $restartNginx,
        DeleteGunicornConfigScript     $deleteGunicornConfig,
        DeleteProjectFilesScript       $deleteProjectFiles,
    ): void {
        DB::transaction(function () use (
            $deleteNginxConfig, $restartNginx, $deleteGunicornConfig, $deleteProjectFiles,
        ) {
            $server = $this->server->freshLockForUpdate();
            $project = $this->project->freshLockForUpdate();

            $userSsh = $server->sftp($project->serverUsername);
            $rootSsh = $server->sftp('root');

            $deleteNginxConfig->execute($server, $project, $rootSsh);

            $restartNginx->execute($server, $rootSsh);

            $deleteGunicornConfig->execute($server, $project, $rootSsh);

            $deleteProjectFiles->execute($server, $project, $userSsh);
        }, 5);
    }
}
