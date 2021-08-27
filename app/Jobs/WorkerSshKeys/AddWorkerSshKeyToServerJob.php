<?php declare(strict_types=1);

namespace App\Jobs\WorkerSshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Scripts\Root\AddSshKeyToUserScript;
use Illuminate\Support\Facades\DB;

/*
 * TODO: IMPORTANT! I also need a job to update the worker key,
 *       i.e. remove the old one and add the new one.
 *       Just in case I ever need to change it for security reasons.
 *       Also need a general contingency plan in case the database got compromised,
 *       i.e. other full keys got leaked. Which are - WorkerSshKeys, ServerSshKeys and DeploySshKeys.
 */

// TODO: CRITICAL! Cover with tests.

class AddWorkerSshKeyToServerJob extends AbstractRemoteServerJob
{
    /**
     * Execute the job.
     */
    public function handle(AddSshKeyToUserScript $script): void {
        DB::transaction(function () use ($script) {
            $server = $this->lockServer();

            $ssh = $server->sftp();

            // Re-add our worker SSH key to the user.
            $script->execute(
                $server,
                (string) config('servers.worker_user'),
                $server->workerSshKey,
                $ssh,
            );

            // Also add this key to all project users, if the server has any at this point.
            /** @var Project $project */
            foreach ($server->projects as $project) {
                $script->execute(
                    $server,
                    $project->serverUsername,
                    $server->workerSshKey,
                    $ssh,
                );
            }
        }, 5);
    }
}
