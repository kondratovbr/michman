<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\DataTransferObjects\ProjectRepoData;
use App\Jobs\Projects\InstallProjectToServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\DB;

class SetupProjectRepoAction
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

            InstallProjectToServerJob::dispatch($project, $server);

            return $project;
        }, 5);
    }
}
