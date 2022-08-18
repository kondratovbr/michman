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
use App\Scripts\Root\UpdateProjectNginxConfigOnServerScript;
use App\Scripts\User\PullDeploymentCommitScript;
use App\Scripts\User\RunDeploymentScriptScript;
use App\Scripts\User\UpdateProjectDeployScriptOnServerScript;
use App\Scripts\User\UpdateProjectEnvironmentOnServerScript;
use App\Scripts\User\UpdateProjectGunicornConfigOnServerScript;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: IMPORTANT! Cover with tests!

class PerformDeploymentOnServerJob extends AbstractRemoteServerJob
{
    use Batchable;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment, Server $server)
    {
        parent::__construct($server);

        $this->deployment = $deployment->withoutRelations();
    }

    public function handle(
        UpdateProjectEnvironmentOnServerScript $updateEnvironment,
        UpdateProjectDeployScriptOnServerScript $updateDeployScript,
        UpdateProjectGunicornConfigOnServerScript $updateGunicornConfig,
        UpdateProjectNginxConfigOnServerScript $updateNginxConfig,
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
            $updateEnvironment, $updateDeployScript, $updateGunicornConfig, $updateNginxConfig,
            $pullCommit, $runDeploymentScript, $restartGunicorn, $enableProjectNginxConfig, $restartNginx,
        ) {
            $server = $this->server->freshLockForUpdate();
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

            try {
                $updateEnvironment->execute($server, $project, $userSsh);

                $updateDeployScript->execute($server, $project, $userSsh);

                $updateGunicornConfig->execute($server, $project, $userSsh);

                $updateNginxConfig->execute($server, $project, $rootSsh);

                $pullCommit->execute($server, $deployment, $userSsh);

                $runDeploymentScript->execute($server, $deployment, $userSsh);

                $restartGunicorn->execute($server, $project, $rootSsh);

                $enableProjectNginxConfig->execute($server, $project, $rootSsh);

                $restartNginx->execute($server, $rootSsh);

                $pivot->successful = true;
            } catch (ServerScriptException) {
                // TODO: Maybe use the exception message as an additional piece of information for the user. I.e. store it in a Deployment pivot model and show in the failure notification or in the deployment log view.
                $pivot->successful = false;
            } finally {
                $pivot->finishedAt = now();
                $pivot->save();

                $deployment->touch();
                $server->touch();
            }

        });
    }
}
