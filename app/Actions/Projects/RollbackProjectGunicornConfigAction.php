<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

// TODO: CRITICAL! Cover with tests.

class RollbackProjectGunicornConfigAction
{
    public function __construct(
       protected UpdateProjectGunicornConfigAction $updateAction,
    ) {}

    public function execute(Project $project): string|null
    {
        $deployment = $project->getCurrentDeployment();

        if (is_null($deployment))
            return null;

        $this->updateAction->execute($project, $deployment->gunicornConfig);

        return $deployment->gunicornConfig;
    }
}
