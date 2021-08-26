<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class UpdateProjectGunicornConfigAction
{
    public function execute(Project $project, string $config): void
    {
        DB::transaction(function () use ($project, $config) {
            /** @var Project $project */
            $project = $project->newQuery()->lockForUpdate()->findOrFail($project->getKey());

            $project->gunicornConfig = $config;
            $project->save();
        }, 5);
    }
}
