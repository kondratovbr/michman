<?php declare(strict_types=1);

namespace App\Jobs\Deployments;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\Root\RestartGunicornScript;
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
        RestartGunicornScript $restartGunicornScript,
    ): void {
        DB::transaction(function () use (
            $pullCommit, $runDeploymentScript, $restartGunicornScript
        ) {
            $server = $this->lockServer();
            /** @var Deployment $deployment */
            $deployment = Deployment::query()
                ->with('project')
                ->lockForUpdate()
                ->findOrFail($this->deployment->getKey());
            $project = $deployment->project;

            $userSsh = $server->sftp($project->serverUsername);

            // TODO: CRITICAL! CONTINUE!

            $pullCommit->execute($server, $deployment, $userSsh);

            $runDeploymentScript->execute($server, $deployment, $userSsh);

            $restartGunicornScript->execute($server, $project);

            //
        }, 5);
    }
}
