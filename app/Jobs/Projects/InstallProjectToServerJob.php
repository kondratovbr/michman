<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Notifications\Projects\ProjectInstallationFailedNotification;
use App\Scripts\Root\ConfigureGunicornScript;
use App\Scripts\User\CloneGitRepoScript;
use App\Scripts\User\CreateProjectVenvScript;
use Illuminate\Support\Facades\DB;

// TODO: IMPORTANT! Cover with test.

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

    public function handle(
        CloneGitRepoScript $cloneRepo,
        CreateProjectVenvScript $createVenv,
        ConfigureGunicornScript $configureGunicorn,
    ): void {
        $api = $this->project->vcsProvider->api();

        DB::transaction(function () use (
            $cloneRepo, $createVenv, $configureGunicorn,
            $api,
        ) {
            $project = $this->project->freshLockForUpdate();
            $server = $this->server->freshLockForUpdate();

            $userSsh = $server->sftp($project->serverUsername);
            $rootSsh = $server->sftp();

            $cloneRepo->execute($server, $project, $userSsh, $api);

            /*
             * TODO: CRITICAL! I'm creating a venv inside the project directory.
             *       Make sure git won't break it on git pull during deployment.
             *       Will it work is a project repo has "venv" directory for some reason?
             *       Should probably move the venv somewhere else.
             */
            $createVenv->execute($server, $project, $userSsh);

            $configureGunicorn->execute($server, $project, $rootSsh);
        }, 5);
    }

    public function failed(): void
    {
        $this->project->user->notify(new ProjectInstallationFailedNotification($this->project));
    }
}
