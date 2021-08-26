<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class UpdateProjectEnvironmentAction
{
    public function execute(Project $project, string $environment): void
    {
        DB::transaction(function () use ($project, $environment) {
            $project = $project->freshLockForUpdate();

            $project->environment = $environment;
            $project->save();
        }, 5);
    }
}
