<?php

namespace Tests\Feature\Policies;

use App\Models\Server;
use App\Models\User;
use App\Policies\ServerPolicy;
use Tests\AbstractFeatureTest;

class ServerPolicyTest extends AbstractFeatureTest
{
    public function test_successful_create_action()
    {
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ServerPolicy $policy */
        $policy = $this->app->make(ServerPolicy::class);

        $result = $policy->create($user);

        $this->assertTrue($result);
    }

    public function test_successful_update_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var ServerPolicy $policy */
        $policy = $this->app->make(ServerPolicy::class);

        $result = $policy->update($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_update_action_with_server_owned_by_different_user()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ServerPolicy $policy */
        $policy = $this->app->make(ServerPolicy::class);

        $result = $policy->update($user, $server);

        $this->assertFalse($result);
    }
}
