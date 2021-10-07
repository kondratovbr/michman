<?php

namespace Tests\Feature\Policies;

use App\Models\Daemon;
use App\Models\Server;
use App\Models\User;
use App\Policies\DaemonPolicy;
use Tests\AbstractFeatureTest;

class DaemonPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->index($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_index_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->index($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_create_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->create($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_view_action()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->view($daemon->user, $daemon);

        $this->assertTrue($result);
    }

    public function test_view_action_with_daemon_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->view($user, $daemon);

        $this->assertFalse($result);
    }

    public function test_successful_update_action()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->update($daemon->user, $daemon);

        $this->assertTrue($result);
    }

    public function test_update_action_with_daemon_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->update($user, $daemon);

        $this->assertFalse($result);
    }

    public function test_successful_delete_action()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->delete($daemon->user, $daemon);

        $this->assertTrue($result);
    }

    public function test_delete_action_with_daemon_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        /** @var DaemonPolicy $policy */
        $policy = $this->app->make(DaemonPolicy::class);

        $result = $policy->delete($user, $daemon);

        $this->assertFalse($result);
    }
}
