<?php declare(strict_types=1);

namespace App\Jobs\Projects;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\IsInternal;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

// TODO: Cover with tests.

class DeleteProjectJob extends AbstractJob
{
    use IsInternal;

    protected Project $project;

    public function __construct(Project $project)
    {
        parent::__construct();

        $this->project = $project;
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $project = $this->project->freshLockForUpdate();

            $project->servers()->sync([]);

            $project->deploySshKey()->delete();

            $project->purge();
        }, 5);
    }
}
