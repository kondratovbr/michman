<?php

namespace Tests\Feature\Policies;

use App\Models\FirewallRule;
use App\Models\Server;
use App\Models\User;
use App\Policies\FirewallRulePolicy;
use Tests\AbstractFeatureTest;

class FirewallRulePolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->index($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_index_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->index($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_create_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->create($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_delete_action()
    {
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory()->withServer()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->delete($rule->user, $rule);

        $this->assertTrue($result);
    }

    public function test_delete_action_with_rule_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory()->withServer()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->delete($user, $rule);

        $this->assertfalse($result);
    }

    public function test_delete_action_with_firewall_rule_that_is_marked_as_undeletable()
    {
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory([
            'can_delete' => false,
        ])->withServer()->create();

        /** @var FirewallRulePolicy $policy */
        $policy = $this->app->make(FirewallRulePolicy::class);

        $result = $policy->delete($rule->user, $rule);

        $this->assertFalse($result);
    }
}
