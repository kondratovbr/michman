<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

// TODO: CRITICAL! Cover with tests.

class RollbackProjectEnvironmentAction
{
    public function __construct(
        protected UpdateProjectEnvironmentAction $updateAction,
    ) {}

    public function execute(Project $project): string|null
    {
        $deployment = $project->getCurrentDeployment();

        if (is_null($deployment))
            return null;

        $this->updateAction->execute($project, $deployment->environment);

        return $deployment->environment;
    }
}
