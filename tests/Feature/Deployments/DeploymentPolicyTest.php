<?php

namespace Tests\Feature\Deployments;

use App\Models\Deployment;
use App\Models\Project;
use App\Models\User;
use App\Policies\DeploymentPolicy;
use Tests\AbstractFeatureTest;

class DeploymentPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var DeploymentPolicy $policy */
        $policy = $this->app->make(DeploymentPolicy::class);

        $result = $policy->index($project->user, $project);

        $this->assertTrue($result);
    }

    public function test_index_action_with_project_owned_by_different_user()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var DeploymentPolicy $policy */
        $policy = $this->app->make(DeploymentPolicy::class);

        $result = $policy->index($user, $project);

        $this->assertFalse($result);
    }

    public function test_successful_view_action()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory()->withProject()->create();

        /** @var DeploymentPolicy $policy */
        $policy = $this->app->make(DeploymentPolicy::class);

        $result = $policy->view($deployment->user, $deployment);

        $this->assertTrue($result);
    }

    public function test_view_action_with_deployment_owned_by_different_user()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory()->withProject()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var DeploymentPolicy $policy */
        $policy = $this->app->make(DeploymentPolicy::class);

        $result = $policy->view($user, $deployment);

        $this->assertFalse($result);
    }

    public function test_successful_deploy_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var DeploymentPolicy $policy */
        $policy = $this->app->make(DeploymentPolicy::class);

        $result = $policy->deploy($project->user, $project);

        $this->assertTrue($result);
    }

    public function test_deploy_action_with_project_owned_by_different_user()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var DeploymentPolicy $policy */
        $policy = $this->app->make(DeploymentPolicy::class);

        $result = $policy->deploy($user, $project);

        $this->assertFalse($result);
    }
}
