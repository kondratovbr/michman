<?php declare(strict_types=1);

namespace App\Jobs\Deployments;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Deployment;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

/*
 * TODO: CRITICAL! Maybe move this logic to an action, so we can quickly retrieve
 *       the latest project's commit hash in this exact moment and immediately
 *       show the user some errors if the repo is unavailable.
 */

class PerformDeploymentJob extends AbstractJob
{
    use InteractsWithVcsProviders;

    protected Deployment $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->setQueue('providers');

        $this->deployment = $deployment->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            /** @var Deployment $deployment */
            $deployment = Deployment::query()
                ->with(['project', 'servers'])
                ->lockForUpdate()
                ->findOrFail($this->deployment->getKey());
            $project = $deployment->project;

            $deployment->commit = $project->vcsProvider->api()
                ->getLatestCommitHash($project->repo, $project->branch);
            $deployment->save();

            $jobs = $deployment->servers->map(
                fn(Server $server) => new PerformDeploymentOnServerJob($deployment, $server)
            );

            Bus::batch($jobs)->onQueue($jobs->first()->queue)->dispatch();
        }, 5);
    }
}
