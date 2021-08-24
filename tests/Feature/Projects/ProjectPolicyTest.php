<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Tests\AbstractFeatureTest;

class ProjectPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var ProjectPolicy $policy */
        $policy = $this->app->make(ProjectPolicy::class);

        $result = $policy->index($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_index_action_with_server_owned_by_different_user()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ProjectPolicy $policy */
        $policy = $this->app->make(ProjectPolicy::class);

        $result = $policy->index($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var ProjectPolicy $policy */
        $policy = $this->app->make(ProjectPolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_create_action_with_server_owned_by_different_user()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ProjectPolicy $policy */
        $policy = $this->app->make(ProjectPolicy::class);

        $result = $policy->create($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_update_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var ProjectPolicy $policy */
        $policy = $this->app->make(ProjectPolicy::class);

        $result = $policy->update($project->user, $project);

        $this->assertTrue($result);
    }

    public function test_update_actions_with_project_owned_by_different_user()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ProjectPolicy $policy */
        $policy = $this->app->make(ProjectPolicy::class);

        $result = $policy->update($user, $project);

        $this->assertFalse($result);
    }
}
