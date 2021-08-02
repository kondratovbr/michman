<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Scripts\User\RetrieveProjectEnvironmentFromServerScript;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class ReloadProjectEnvironmentAction
{
    public function __construct(
        protected RetrieveProjectEnvironmentFromServerScript $retrieveScript,
        protected UpdateProjectEnvironmentAction $updateAction,
    ) {}

    public function execute(Project $project): string
    {
        return DB::transaction(function () use ($project):string {
            /** @var Project $project */
            $project = $project->newQuery()
                ->with(['servers' => function (Relation $query) {
                    $query->oldest();
                }])
                ->lockForUpdate()
                ->findOrFail($project->getKey());

            if ($project->servers->count() == 0)
                return '';

            $environment = $this->retrieveScript->execute($project->servers->first(), $project);

            if ($environment !== $project->environment)
                $this->updateAction->execute($project, $environment);

            return $environment;
        }, 5);
    }
}
