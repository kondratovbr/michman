<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Root\ConfigureGunicornScript;
use App\Scripts\Root\EnablePlaceholderSiteScript;
use App\Scripts\Root\RestartNginxScript;
use App\Scripts\User\CloneGitRepoScript;
use App\Scripts\User\CreateProjectVenvScript;
use App\Scripts\User\UpdateProjectDeployScriptOnServerScript;
use App\Scripts\User\UpdateProjectEnvironmentOnServerScript;
use App\Scripts\User\UploadPlaceholderPageScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with test.

class InstallProjectToServerJob extends AbstractRemoteServerJob
{
    protected Project $project;
    protected bool $installDependencies;

    public function __construct(Project $project, Server $server, bool $installDependencies)
    {
        parent::__construct($server);

        $this->project = $project->withoutRelations();
        $this->installDependencies = $installDependencies;
    }

    /**
     * Execute the job.
     */
    public function handle(
        CloneGitRepoScript $cloneRepo,
        CreateProjectVenvScript $createVenv,
        ConfigureGunicornScript $configureGunicorn,
        UpdateProjectEnvironmentOnServerScript $updateEnv,
        UpdateProjectDeployScriptOnServerScript $updateDeployScript,
        UploadPlaceholderPageScript $uploadPlaceholderPage,
        EnablePlaceholderSiteScript $enablePlaceholderSite,
        RestartNginxScript $restartNginx,
    ): void {
        DB::transaction(function () use (
            $cloneRepo, $createVenv, $configureGunicorn, $updateEnv, $updateDeployScript,
            $uploadPlaceholderPage, $enablePlaceholderSite, $restartNginx,
        ) {
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($this->project->getKey());
            /** @var Server $server */
            $server = $project->servers()->lockForUpdate()->findOrFail($this->server->getKey());

            $userSsh = $server->sftp($project->serverUsername);
            $rootSsh = $server->sftp();

            $cloneRepo->execute($server, $project, $userSsh);

            /*
             * TODO: CRITICAL! I'm creating a venv inside the project directory.
             *       Make sure git won't break it on git pull during deployment.
             *       Will it work is a project repo has "venv" directory for some reason?
             *       Should probably move the venv somewhere else.
             */
            $createVenv->execute($server, $project, $userSsh);

            $updateEnv->execute($server, $project, $userSsh);

            $updateDeployScript->execute($server, $project, $userSsh);

            $configureGunicorn->execute($server, $project, $rootSsh);

            $uploadPlaceholderPage->execute($server, $project, $userSsh);

            $enablePlaceholderSite->execute($server, $project, $rootSsh);

            $restartNginx->execute($server, $rootSsh);

        }, 5);
    }
}
