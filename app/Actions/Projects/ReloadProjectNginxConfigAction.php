<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Scripts\Root\RetrieveProjectNginxConfigFromServerScript;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class ReloadProjectNginxConfigAction
{
    public function __construct(
        protected RetrieveProjectNginxConfigFromServerScript $retrieveScript,
        protected UpdateProjectNginxConfigAction $updateAction,
    ) {}

    public function execute(Project $project): string|null
    {
        return DB::transaction(function () use ($project):string|null {
            /** @var Project $project */
            $project = $project->newQuery()
                ->with(['servers' => function (Relation $query) {
                    $query->oldest();
                }])
                ->lockForUpdate()
                ->findOrFail($project->getKey());

            if ($project->servers->count() == 0)
                return null;

            $nginxConfig = $this->retrieveScript->execute($project->servers->first(), $project);

            if ($nginxConfig !== $project->nginxConfig)
                $this->updateAction->execute($project, $nginxConfig);

            return $nginxConfig;
        }, 5);
    }
}
