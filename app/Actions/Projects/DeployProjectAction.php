<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Actions\Deployments\StoreDeploymentAction;
use App\DataTransferObjects\DeploymentDto;
use App\Models\Deployment;
use App\Models\Project;

// TODO: IMPORTANT! Figure out how to fail gracefully here if we can't get the commit hash and how to communicate this to the user.

// TODO: CRITICAL! Cover with tests!

class DeployProjectAction
{
    public function __construct(
        protected StoreDeploymentAction $store,
    ) {}

    public function execute(Project $project, string $commit = null, bool $auto = false): Deployment
    {
        $commit ??= $project->vcsProvider->api()
            ->getLatestCommitHash($project->repo, $project->branch);

        return $this->store->execute(new DeploymentDto(
            type: $auto ? Deployment::TYPE_AUTO : Deployment::TYPE_MANUAL,
            branch: $project->branch,
            commit: $commit,
            environment: $project->environment,
            deploy_script: $project->deployScript,
            gunicorn_config: $project->gunicornConfig,
            nginx_config: $project->nginxConfig,
        ), $project);
    }
}
