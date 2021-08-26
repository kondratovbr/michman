<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class UpdateProjectNginxConfigAction
{
    public function execute(Project $project, string $nginxConfig): void
    {
        DB::transaction(function () use ($project, $nginxConfig) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->nginxConfig = $nginxConfig;
            $project->save();
        }, 5);
    }
}
