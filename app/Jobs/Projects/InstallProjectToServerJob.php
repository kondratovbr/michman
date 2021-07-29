<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Root\ConfigureGunicornScript;
use App\Scripts\User\CloneGitRepoScript;
use App\Scripts\User\CreateProjectVenvScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with test.

class InstallProjectToServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Project $project;
    protected Server $server;
    protected bool $installDependencies;

    public function __construct(Project $project, Server $server, bool $installDependencies)
    {
        $this->setQueue('servers');

        $this->project = $project->withoutRelations();
        $this->server = $server->withoutRelations();
        $this->installDependencies = $installDependencies;
    }

    /**
     * Execute the job.
     */
    public function handle(
        CloneGitRepoScript $cloneRepo,
        CreateProjectVenvScript $createVenv,
        ConfigureGunicornScript $configureGunicorn,
    ): void {
        DB::transaction(function () use (
            $cloneRepo, $createVenv, $configureGunicorn
        ) {
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($this->project->getKey());
            /** @var Server $server */
            $server = $project->servers()->lockForUpdate()->findOrFail($this->server->getKey());

            $userSsh = $server->sftp($project->serverUsername);

            $cloneRepo->execute($server, $project, $userSsh,);

            /*
             * TODO: CRITICAL! I'm creating a venv inside the project directory.
             *       Make sure git won't break it on git pull during deployment.
             */
            $createVenv->execute(
                $server,
                $project,
                $this->installDependencies,
                $userSsh,
            );

            $configureGunicorn->execute($server, $project);

        }, 5);
    }
}
