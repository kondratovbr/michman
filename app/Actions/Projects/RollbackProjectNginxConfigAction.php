<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

// TODO: CRITICAL! Cover with tests.

class RollbackProjectNginxConfigAction
{
    public function __construct(
        protected UpdateProjectNginxConfigAction $updateAction,
    ) {}

    public function execute(Project $project): string|null
    {
        $deployment = $project->getCurrentDeployment();

        if (is_null($deployment))
            return null;

        $this->updateAction->execute($project, $deployment->nginxConfig);

        return $deployment->nginxConfig;
    }
}
