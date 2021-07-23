<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\ProjectRepoData;
use App\Jobs\Projects\InstallProjectToServerJob;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
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

            if (! $project->useDeployKey)
                $jobs[] = new AddServerSshKeyToVcsJob($server, $vcsProvider);

            // TODO: CRITICAL! CONTINUE. I should check that the repo is available from the server somewhere here in a different server and provide a feedback to the user.

            $jobs[] = new InstallProjectToServerJob($project, $server);

            Bus::chain($jobs)->dispatch();

            return $project;
        }, 5);
    }
}
