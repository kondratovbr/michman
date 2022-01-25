<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Actions\Workers\DeleteAllWorkersAction;
use App\Jobs\Deployments\DeleteProjectDeploymentsJob;
use App\Jobs\DeploySshKeys\DeleteDeploySshKeyFromServerJob;
use App\Jobs\Projects\RemoveRepoDataFromProjectJob;
use App\Jobs\Projects\UninstallProjectFromServerJob;
use App\Jobs\ServerSshKeys\DeleteServerSshKeyFromVcsJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class UninstallProjectRepoAction
{
    public function __construct(
        private DeleteAllWorkersAction $deleteWorkers,
    ) {}

    public function execute(Project $project, bool $returnJobs = false): Collection|null
    {
        return DB::transaction(function () use ($project, $returnJobs): Collection|null {
            $project->freshLockForUpdate('servers');

            $jobs = $this->deleteWorkers->execute($project, true);

            /** @var Server $server */
            foreach ($project->servers as $server) {
                if ($project->useDeployKey) {
                    $jobs[] = new DeleteDeploySshKeyFromServerJob($server, $project);
                } else {
                    $jobs[] = new DeleteServerSshKeyFromVcsJob($server, $project->vcsProvider);
                }

                $jobs[] = new UninstallProjectFromServerJob($project, $server);
            }

            $jobs[] = new DeleteProjectDeploymentsJob($project);

            $jobs[] = new RemoveRepoDataFromProjectJob($project);

            $project->removingRepo = true;
            $project->save();

            if ($returnJobs)
                return new Collection($jobs);

            Bus::chain($jobs)->dispatch();

            return null;
        }, 5);
    }
}
