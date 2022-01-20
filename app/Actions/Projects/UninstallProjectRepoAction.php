<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\DeploySshKeys\DeleteDeploySshKeyFromServerJob;
use App\Jobs\Projects\RemoveRepoDataFromProject;
use App\Jobs\Projects\UninstallProjectFromServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class UninstallProjectRepoAction
{
    public function execute(Project $project): Project
    {
        // TODO: CRITICAL! CONTINUE. Implement and test.

        return DB::transaction(function () use ($project): Project {
            $project->freshLockForUpdate('servers');

            $jobs = [];

            /** @var Server $server */
            foreach ($project->servers as $server) {
                if ($project->useDeployKey)
                    $jobs[] = new DeleteDeploySshKeyFromServerJob($server, $project);

                $jobs[] = new UninstallProjectFromServerJob($project, $server);
            }

            $jobs[] = new RemoveRepoDataFromProject($project);

            Bus::chain($jobs)->dispatch();

            return $project;
        }, 5);
    }
}
