<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\Projects\DeleteProjectJob;
use App\Jobs\Servers\DeleteUserFromServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class DeleteProjectAction
{
    public function __construct(
        protected UninstallProjectRepoAction $uninstallRepo,
    ) {}

    public function execute(Project $project, bool $returnJobs = false): Collection|null
    {
        return DB::transaction(function () use ($project, $returnJobs): Collection|null {
            $project = $project->freshLockForUpdate();

            $jobs = new Collection;

            if ($project->repoInstalled && ! $project->removingRepo) {
                $jobs = $jobs->concat($this->uninstallRepo->execute($project, true));
            }

            $jobs = $jobs->concat($project->servers->map(
                fn(Server $server) => new DeleteUserFromServerJob($server, $project->serverUsername)
            ));

            $jobs->push(new DeleteProjectJob($project));

            if ($returnJobs)
                return $jobs;

            Bus::chain($jobs->toArray())->dispatch();

            return null;
        }, 5);
    }
}
