<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Actions\Webhooks\DeleteProjectWebhookAction;
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

// TODO: Even just running this action may take some time. Better offload the logic to a job. Same for the whole project deletion action.

class UninstallProjectRepoAction
{
    public function __construct(
        private DeleteAllWorkersAction $deleteWorkers,
        private DeleteProjectWebhookAction $deleteWebhook,
    ) {}

    public function execute(Project $project, bool $returnJobs = false): Collection|null
    {
        return DB::transaction(function () use ($project, $returnJobs): Collection|null {
            $project->freshLockForUpdate('servers');

            $jobs = $this->deleteWorkers->execute($project, true);

            if (isset($project->webhook))
                $jobs->push($this->deleteWebhook->execute($project->webhook, true));

            /** @var Server $server */
            foreach ($project->servers as $server) {
                if ($project->useDeployKey) {
                    $jobs->push(new DeleteDeploySshKeyFromServerJob($server, $project));
                } else {
                    $jobs->push(new DeleteServerSshKeyFromVcsJob($server, $project->vcsProvider));
                }

                $jobs->push(new UninstallProjectFromServerJob($project, $server));
            }

            $jobs->push(new DeleteProjectDeploymentsJob($project));

            $jobs->push(new RemoveRepoDataFromProjectJob($project));

            $project->removingRepo = true;
            $project->save();

            if ($returnJobs)
                return new Collection($jobs);

            Bus::chain($jobs->toArray())->dispatch();

            return null;
        }, 5);
    }
}
