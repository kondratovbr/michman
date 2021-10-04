<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class UpdateProjectDeploymentBranchAction
{
    public function execute(Project $project, string $branch): void
    {
        DB::transaction(function () use ($project, $branch) {
            $project = $project->freshLockForUpdate();

            $project->branch = $branch;
            $project->save();
        }, 5);
    }
}
