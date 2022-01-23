<?php declare(strict_types=1);

namespace App\Jobs\Deployments;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DeleteProjectDeploymentsJob extends AbstractJob
{
    use IsInternal;

    protected Project $project;

    public function __construct(Project $project)
    {
        $this->setQueue('default');

        $this->project = $project->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $project = $this->project->freshLockForUpdate('deployments');

            /** @var Deployment $deployment */
            foreach ($project->deployments as $deployment) {
                $deployment->servers()->detach();
                $deployment->delete();
            }
        }, 5);
    }
}
