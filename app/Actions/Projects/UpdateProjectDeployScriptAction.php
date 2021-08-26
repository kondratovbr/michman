<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class UpdateProjectDeployScriptAction
{
    public function execute(Project $project, string $script): void
    {
        DB::transaction(function () use ($project, $script) {
            $project = $project->freshLockForUpdate();

            $project->deployScript = $script;
            $project->save();
        }, 5);
    }
}
