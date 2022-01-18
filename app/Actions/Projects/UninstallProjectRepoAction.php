<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Jobs\DeploySshKeys\DeleteDeploySshKeyFromServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class UninstallProjectRepoAction
{
    public function execute(Project $project): Project
    {
        // TODO: CRITICAL! CONTINUE. Implement.

        return DB::transaction(function () use ($project): Project {
            $project->freshLockForUpdate('servers');

            $jobs = [];

            /** @var Server $server */
            foreach ($project->servers as $server) {
                if ($project->useDeployKey)
                    $jobs[] = new DeleteDeploySshKeyFromServerJob($server, $project);

                // Revert Nginx config to a placeholder and restart it
                // Stop gunicorn service, remove config and service
                // Remove project files
                //
            }

            //

            Bus::chain($jobs)->dispatch();

            return $project;
        }, 5);
    }
}
