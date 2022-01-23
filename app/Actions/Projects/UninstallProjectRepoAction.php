<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Actions\Workers\DeleteAllWorkersAction;
use App\Jobs\Deployments\DeleteProjectDeploymentsJob;
use App\Jobs\DeploySshKeys\DeleteDeploySshKeyFromServerJob;
use App\Jobs\Projects\RemoveRepoDataFromProject;
use App\Jobs\Projects\UninstallProjectFromServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class UninstallProjectRepoAction
{
    public function __construct(
        private DeleteAllWorkersAction $deleteWorkers,
    ) {}

    public function execute(Project $project): Project
    {
        return DB::transaction(function () use ($project): Project {
            $project->freshLockForUpdate('servers');


            $jobs = $this->deleteWorkers->execute($project, true);

            /** @var Server $server */
            foreach ($project->servers as $server) {
                if ($project->useDeployKey)
                    $jobs[] = new DeleteDeploySshKeyFromServerJob($server, $project);

                $jobs[] = new UninstallProjectFromServerJob($project, $server);
            }

            $jobs[] = new DeleteProjectDeploymentsJob($project);

            $jobs[] = new RemoveRepoDataFromProject($project);

            Bus::chain($jobs)->dispatch();

            $project->removingRepo = true;
            $project->save();

            return $project;
        }, 5);
    }
}
