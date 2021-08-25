<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Scripts\User\RetrieveProjectGunicornConfigFromServerScript;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class ReloadProjectGunicornConfigAction
{
    public function __construct(
        protected RetrieveProjectGunicornConfigFromServerScript $retrieveScript,
        protected UpdateProjectGunicornConfigAction $updateAction,
    ) {}

    public function execute(Project $project): string|null
    {
        // TODO: CRITICAL! Implement, test and cover with test.

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

            $gunicornConfig = $this->retrieveScript->execute($project->servers->first(), $project);

            if ($gunicornConfig !== $project->deployScript)
                $this->updateAction->execute($project, $gunicornConfig);

            return $gunicornConfig;
        }, 5);
    }
}
