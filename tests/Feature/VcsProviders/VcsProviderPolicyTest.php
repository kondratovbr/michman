<?php

namespace Tests\Feature\VcsProviders;

use App\Models\User;
use App\Models\VcsProvider;
use App\Policies\VcsProviderPolicy;
use Tests\AbstractFeatureTest;

class VcsProviderPolicyTest extends AbstractFeatureTest
{
    public function test_successful_create_action()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var VcsProviderPolicy $policy */
        $policy = $this->app->make(VcsProviderPolicy::class);

        $result = $policy->create($user, 'github');

        $this->assertTrue($result);
    }

    public function test_create_action_for_user_that_already_has_this_vcs_provider()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github',
        ])->for($user)->create();

        /** @var VcsProviderPolicy $policy */
        $policy = $this->app->make(VcsProviderPolicy::class);

        $result = $policy->create($user, 'github');

        $this->assertFalse($result);
    }

    public function test_successful_update_action()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        $vcsProvider = VcsProvider::factory()->for($user)->create();

        /** @var VcsProviderPolicy $policy */
        $policy = $this->app->make(VcsProviderPolicy::class);

        $result = $policy->update($user, $vcsProvider);

        $this->assertTrue($result);
    }

    public function test_update_action_for_vcs_provider_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        $vcsProvider = VcsProvider::factory()->withUser()->create();

        /** @var VcsProviderPolicy $policy */
        $policy = $this->app->make(VcsProviderPolicy::class);

        $result = $policy->update($user, $vcsProvider);

        $this->assertFalse($result);
    }
}
