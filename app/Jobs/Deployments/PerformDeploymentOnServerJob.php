<?php declare(strict_types=1);

namespace App\Jobs\Deployments;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\Root\EnableProjectNginxConfigScript;
use App\Scripts\Root\RestartGunicornScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\User\PullDeploymentCommitScript;
use App\Scripts\User\RunDeploymentScriptScript;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class PerformDeploymentOnServerJob extends AbstractRemoteServerJob
{
    use Batchable;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment, Server $server)
    {
        parent::__construct($server);

        $this->deployment = $deployment->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(
        PullDeploymentCommitScript $pullCommit,
        RunDeploymentScriptScript $runDeploymentScript,
        RestartGunicornScript $restartGunicorn,
        EnableProjectNginxConfigScript $enableProjectNginxConfig,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $pullCommit, $runDeploymentScript, $restartGunicorn, $enableProjectNginxConfig, $restartNginx,
        ) {
            $server = $this->lockServer();
            /** @var Deployment $deployment */
            $deployment = Deployment::query()
                ->with('project')
                ->lockForUpdate()
                ->findOrFail($this->deployment->getKey());
            $project = $deployment->project;

            $userSsh = $server->sftp($project->serverUsername);
            $rootSsh = $server->sftp();

            $pullCommit->execute($server, $deployment, $userSsh);

            // TODO: CRITICAL! CONTINUE. Figure out why my project has null in "root" column in the DB and implement that part - use that property in Nginx config to set up static files.

            // TODO: CRITICAL! CONTINUE! Try again with the new .env files. If works - retry the whole process to make sure the default .env is correct as well. Then - figure out actual Nginx config and its uploading. Then - figure out how to fail the deployment if the user's script is broken and how to show (and store) deployment logs.

            $runDeploymentScript->execute($server, $deployment, $userSsh);

            $restartGunicorn->execute($server, $project, $rootSsh);

            $enableProjectNginxConfig->execute($server, $project, $rootSsh);

            $restartNginx->execute($server, $rootSsh);

        }, 5);
    }
}
