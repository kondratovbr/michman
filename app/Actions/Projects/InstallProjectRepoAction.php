<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\ProjectRepoData;
use App\Jobs\Projects\InstallProjectToServerJob;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

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

            $project->save();

            $jobs = [];

            if (! $project->useDeployKey) {
                $jobs[] = new UploadServerSshKeyToServerJob($server, $project->serverUsername);
                $jobs[] = new AddServerSshKeyToVcsJob($server, $vcsProvider);
            }

            // TODO: CRITICAL! CONTINUE. Implement a notification system similar to the Forge's one to notify users about various mishaps with their servers and projects. Forge calls it "Server Alerts".

            $jobs[] = new InstallProjectToServerJob($project, $server);

            Bus::chain($jobs)->dispatch();

            return $project;
        }, 5);
    }
}
