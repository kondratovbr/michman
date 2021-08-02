<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\ProjectRepoData;
use App\Facades\ConfigView;
use App\Jobs\DeploySshKeys\UploadDeploySshKeyToServerJob;
use App\Jobs\Projects\InstallProjectToServerJob;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class InstallProjectRepoAction
{
    public function execute(
        Project $project,
        VcsProvider $vcsProvider,
        ProjectRepoData $data,
        Server $server,
        bool $installDependencies,
    ): Project {
        return DB::transaction(function () use (
            $project, $vcsProvider, $data, $server, $installDependencies
        ): Project {
            /** @var Project $project */
            $project = Project::query()->lockForUpdate()->findOrFail($project->getKey());

            $project->vcsProvider()->associate($vcsProvider);
            $project->fill($data->toArray());
            $project->environment = ConfigView::render('default_env_file', ['project' => $project]);
            $project->deployScript = ConfigView::render('default_deploy_script', ['project' => $project]);
            $project->gunicornConfig = ConfigView::render('gunicorn.default_config', ['project' => $project]);
            $project->save();

            $jobs = [];

            if ($project->useDeployKey) {
                $jobs[] = new UploadDeploySshKeyToServerJob($server, $project);
            } else {
                $jobs[] = new UploadServerSshKeyToServerJob($server, $project->serverUsername);
                $jobs[] = new AddServerSshKeyToVcsJob($server, $vcsProvider);
            }

            // TODO: CRITICAL! CONTINUE. Implement a notification system similar to the Forge's one to notify users about various mishaps with their servers and projects. Forge calls it "Server Alerts".

            $jobs[] = new InstallProjectToServerJob($project, $server, $installDependencies);

            Bus::chain($jobs)->dispatch();

            return $project;
        }, 5);
    }
}
