<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Policies\ProviderPolicy;
use Tests\AbstractFeatureTest;

class ProviderPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_user_action()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ProviderPolicy $policy */
        $policy = $this->app->make(ProviderPolicy::class);

        $result = $policy->indexUser($user, $user);

        $this->assertTrue($result);
    }

    public function test_index_user_action_with_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var User $subject */
        $subject = User::factory()->withPersonalTeam()->create();

        /** @var ProviderPolicy $policy */
        $policy = $this->app->make(ProviderPolicy::class);

        $result = $policy->indexUser($user, $subject);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var ProviderPolicy $policy */
        $policy = $this->app->make(ProviderPolicy::class);

        $result = $policy->create($user, $user);

        $this->assertTrue($result);
    }
}
