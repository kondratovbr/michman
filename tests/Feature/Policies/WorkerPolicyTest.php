<?php

namespace Tests\Feature\Policies;

use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use App\Models\Worker;
use App\Policies\WorkerPolicy;
use Tests\AbstractFeatureTest;

class WorkerPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->index($project->user, $project);

        $this->assertTrue($result);
    }

    public function test_index_action_with_project_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->index($user, $project);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->create($project->user, $project);

        $this->assertTrue($result);
    }

    public function test_create_action_with_project_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->create($user, $project);

        $this->assertFalse($result);
    }

    public function test_successful_view_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->view($worker->user, $worker);

        $this->assertTrue($result);
    }

    public function test_view_action_with_worker_owned_by_different_user()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->view($user, $worker);

        $this->assertFalse($result);
    }

    public function test_successful_restart_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->restart($worker->user, $worker);

        $this->assertTrue($result);
    }

    public function test_restart_action_with_worker_owned_by_different_user()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->restart($user, $worker);

        $this->assertFalse($result);
    }

    public function test_successful_delete_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->delete($worker->user, $worker);

        $this->assertTrue($result);
    }

    public function test_delete_action_with_worker_owned_by_different_user()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var WorkerPolicy $policy */
        $policy = $this->app->make(WorkerPolicy::class);

        $result = $policy->delete($user, $worker);

        $this->assertFalse($result);
    }
}
