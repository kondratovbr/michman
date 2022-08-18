<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class RemoveRepoDataFromProjectJob extends AbstractJob
{
    use IsInternal;

    protected Project $project;

    public function __construct(Project $project)
    {
        parent::__construct();

        $this->project = $project->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $project = $this->project->freshLockForUpdate();

            // TODO: Refactoring. Projects need states as well.
            if (! $project->removingRepo)
                return;

            $project->repo = null;
            $project->branch = null;
            $project->package = null;
            $project->useDeployKey = null;
            $project->requirementsFile = null;

            $project->environment = null;
            $project->deployScript = null;
            $project->gunicornConfig = null;
            $project->nginxConfig = null;

            $project->vcsProvider()->disassociate();

            $project->removingRepo = false;

            $project->save();
        });
    }
}
