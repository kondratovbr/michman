<?php

namespace Tests\Feature\Servers;

use App\Models\User;
use App\Policies\ServerPolicy;
use Tests\AbstractFeatureTest;

class ServerPolicyTest extends AbstractFeatureTest
{
    public function test_successful_create_test()
    {
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ServerPolicy $policy */
        $policy = $this->app->make(ServerPolicy::class);

        $result = $policy->create($user);

        $this->assertTrue($result);
    }
}
