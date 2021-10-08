<?php

namespace Tests\Feature\Policies;

use App\Models\Python;
use App\Models\Server;
use App\Models\User;
use App\Policies\PythonPolicy;
use Tests\AbstractFeatureTest;

class PythonPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->index($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_index_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->index($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->create($server->user, $server, '3_9');

        $this->assertTrue($result);
    }

    public function test_create_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->create($user, $server, '3_9');

        $this->assertFalse($result);
    }

    public function test_create_action_with_version_that_already_installed()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        Python::factory([
            'version' => '3_9',
        ])->for($server)->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->create($server->user, $server, '3_9');

        $this->assertFalse($result);
    }

    public function test_successful_update_action()
    {
        /** @var Python $python */
        $python = Python::factory()->withServer()->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->update($python->user, $python);

        $this->assertTrue($result);
    }

    public function test_update_action_with_python_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Python $python */
        $python = Python::factory()->withServer()->create();

        /** @var PythonPolicy $policy */
        $policy = $this->app->make(PythonPolicy::class);

        $result = $policy->update($user, $python);

        $this->assertFalse($result);
    }
}
