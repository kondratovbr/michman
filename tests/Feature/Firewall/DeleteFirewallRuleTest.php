<?php

namespace Tests\Feature\Firewall;

use App\Actions\Firewall\DeleteFirewallRuleAction;
use App\Http\Livewire\Firewall\FirewallIndexTable;
use App\Models\FirewallRule;
use App\Models\Server;
use App\Models\User;
use App\Policies\FirewallRulePolicy;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteFirewallRuleTest extends AbstractFeatureTest
{
    public function test_firewall_rule_can_be_deleted()
    {
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory()->withServer()->create();

        $this->actingAs($rule->user);

        $this->mock(FirewallRulePolicy::class, function (MockInterface $mock) use ($rule) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($rule->user) && $serverArg->is($rule->server)
                )
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('delete')
                ->withArgs(fn(User $userArg, FirewallRule $ruleArg) =>
                    $userArg->is($rule->user) && $ruleArg->is($rule)
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(FirewallIndexTable::class, ['server' => $rule->server])
            ->call('delete',
                Mockery::mock(DeleteFirewallRuleAction::class,
                    function (MockInterface $mock) use ($rule) {
                        $mock->shouldReceive('execute')
                            ->withArgs(fn(FirewallRule $ruleArg) => $ruleArg->is($rule))
                            ->once();
                    }
                ),
                (string) $rule->getKey()
            )
            ->assertOk()
            ->assertHasNoErrors();
    }

    public function test_firewall_rule_from_different_server_cannot_be_deleted()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory()->withServer()->create();

        $this->actingAs($rule->user);

        $this->mock(FirewallRulePolicy::class, function (MockInterface $mock) use ($rule, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($rule->user) && $serverArg->is($server)
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(FirewallIndexTable::class, ['server' => $server])
            ->call('delete',
                Mockery::mock(DeleteFirewallRuleAction::class,
                    function (MockInterface $mock) {
                        $mock->shouldNotHaveBeenCalled();
                    }
                ),
                (string) $rule->getKey()
            )
            ->assertHasErrors('key');
    }

    public function test_firewall_rule_with_empty_key_cannot_be_deleted()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory()->withServer()->create();

        $this->actingAs($rule->user);

        $this->mock(FirewallRulePolicy::class, function (MockInterface $mock) use ($rule, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($rule->user) && $serverArg->is($server)
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(FirewallIndexTable::class, ['server' => $server])
            ->call('delete',
                Mockery::mock(DeleteFirewallRuleAction::class,
                    function (MockInterface $mock) {
                        $mock->shouldNotHaveBeenCalled();
                    }
                ),
                ''
            )
            ->assertHasErrors('key');
    }
}
