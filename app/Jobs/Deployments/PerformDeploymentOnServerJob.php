<?php declare(strict_types=1);

namespace App\Jobs\Deployments;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Deployment;
use App\Models\DeploymentServerPivot;
use App\Models\Server;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\EnableProjectNginxConfigScript;
use App\Scripts\Root\RestartGunicornScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\User\PullDeploymentCommitScript;
use App\Scripts\User\RunDeploymentScriptScript;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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
        /** @var DeploymentServerPivot $pivot */
        $pivot = $this->deployment->servers->find($this->server->getKey())?->serverDeployment;

        if (is_null($pivot))
            throw new RuntimeException('The deployment doesn\'t have the requested server attached to it.');

        $pivot->startedAt = now();
        $pivot->save();

        DB::transaction(function () use (
            $pullCommit, $runDeploymentScript, $restartGunicorn, $enableProjectNginxConfig, $restartNginx,
        ) {
            $server = $this->lockServer();
            /** @var Deployment $deployment */
            $deployment = Deployment::query()->lockForUpdate()->findOrFail($this->deployment->getKey());
            $project = $deployment->project;

            if (! $deployment->servers->contains($server))
                throw new RuntimeException('The deployment doesn\'t have the requested server attached to it.');

            /** @var DeploymentServerPivot $pivot */
            $pivot = $deployment->servers->find($server->getKey())->serverDeployment;

            $pivot->startedAt = now();
            $pivot->save();

            $userSsh = $server->sftp($project->serverUsername);
            $rootSsh = $server->sftp();

            // TODO: CRITICAL! CONTINUE! Figure out how to fail the deployment if the user's script is broken and how to show (and store) deployment logs.
            //       Try a failing deployment script and see how it goes. Make sure the script that are used here are especially robust and check everything, since they depend on the project.
            try {
                $pullCommit->execute($server, $deployment, $userSsh);

                $runDeploymentScript->execute($server, $deployment, $userSsh);

                $restartGunicorn->execute($server, $project, $rootSsh);

                $enableProjectNginxConfig->execute($server, $project, $rootSsh);

                $restartNginx->execute($server, $rootSsh);

                $pivot->successful = true;
            } catch (ServerScriptException $scriptException) {
                $pivot->successful = false;
                $this->fail($scriptException);
            } finally {
                $pivot->finishedAt = now();
                $pivot->save();
            }

        }, 5);
    }
}
