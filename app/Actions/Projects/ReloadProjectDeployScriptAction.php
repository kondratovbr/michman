<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Scripts\User\RetrieveProjectDeployScriptFromServerScript;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class ReloadProjectDeployScriptAction
{
    public function __construct(
        protected RetrieveProjectDeployScriptFromServerScript $retrieveScript,
        protected UpdateProjectDeployScriptAction $updateAction,
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

            $deployScript = $this->retrieveScript->execute($project->servers->first(), $project);

            if ($deployScript !== $project->deployScript)
                $this->updateAction->execute($project, $deployScript);

            return $deployScript;
        }, 5);
    }
}
