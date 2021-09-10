<?php

namespace Tests\Feature\Firewall;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleDto;
use App\Http\Livewire\Firewall\FirewallCreateForm;
use App\Models\FirewallRule;
use App\Models\Server;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class StoreFirewallRuleTest extends AbstractFeatureTest
{
    public function test_firewall_rule_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        $this->actingAs($user);

        Livewire::test(FirewallCreateForm::class, ['server' => $server])
            ->set('name', 'WHOIS')
            ->set('port', '43')
            ->set('from_ip', '127.0.0.1')
            ->call('store', Mockery::mock(StoreFirewallRuleAction::class,
                function (MockInterface $mock) use ($server) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (
                            FirewallRuleDto $dataArg,
                            Server $serverArg,
                        ) use ($server) {
                            return $dataArg->name === 'WHOIS'
                                && $dataArg->port === '43'
                                && $dataArg->from_ip === '127.0.0.1'
                                && $dataArg->can_delete === true
                                && $serverArg->is($server);
                        })
                        ->once()
                        ->andReturn(new FirewallRule);
                }
            ))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertEmitted('firewall-rule-stored')
            ->assertSet('name', null)
            ->assertSet('port', null)
            ->assertSet('from_ip', null);
    }

    public function test_firewall_rule_with_empty_name_cannot_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        $this->actingAs($user);

        Livewire::test(FirewallCreateForm::class, ['server' => $server])
            ->set('name', '')
            ->set('port', '43')
            ->set('from_ip', '127.0.0.1')
            ->call('store', Mockery::mock(StoreFirewallRuleAction::class,
                function (MockInterface $mock) use ($server) {
                    $mock->shouldNotHaveBeenCalled();
                }
            ))
            ->assertHasErrors('name');
    }

    public function test_firewall_rule_with_empty_port_cannot_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        $this->actingAs($user);

        Livewire::test(FirewallCreateForm::class, ['server' => $server])
            ->set('name', 'WHOIS')
            ->set('port', '')
            ->set('from_ip', '127.0.0.1')
            ->call('store', Mockery::mock(StoreFirewallRuleAction::class,
                function (MockInterface $mock) use ($server) {
                    $mock->shouldNotHaveBeenCalled();
                }
            ))
            ->assertHasErrors('port');
    }

    public function test_firewall_rule_with_empty_ip_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        $this->actingAs($user);

        Livewire::test(FirewallCreateForm::class, ['server' => $server])
            ->set('name', 'WHOIS')
            ->set('port', '43')
            ->set('from_ip', '')
            ->call('store', Mockery::mock(StoreFirewallRuleAction::class,
                function (MockInterface $mock) use ($server) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (
                            FirewallRuleDto $dataArg,
                            Server $serverArg,
                        ) use ($server) {
                            return $dataArg->name === 'WHOIS'
                                && $dataArg->port === '43'
                                && $dataArg->from_ip === null
                                && $dataArg->can_delete === true
                                && $serverArg->is($server);
                        })
                        ->once()
                        ->andReturn(new FirewallRule);
                }
            ))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertEmitted('firewall-rule-stored')
            ->assertSet('name', null)
            ->assertSet('port', null)
            ->assertSet('from_ip', null);
    }

    public function test_firewall_rule_with_range_of_ports_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        $this->actingAs($user);

        Livewire::test(FirewallCreateForm::class, ['server' => $server])
            ->set('name', 'WHOIS')
            ->set('port', '43:45')
            ->set('from_ip', '127.0.0.1')
            ->call('store', Mockery::mock(StoreFirewallRuleAction::class,
                function (MockInterface $mock) use ($server) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (
                            FirewallRuleDto $dataArg,
                            Server $serverArg,
                        ) use ($server) {
                            return $dataArg->name === 'WHOIS'
                                && $dataArg->port === '43:45'
                                && $dataArg->from_ip === '127.0.0.1'
                                && $dataArg->can_delete === true
                                && $serverArg->is($server);
                        })
                        ->once()
                        ->andReturn(new FirewallRule);
                }
            ))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertEmitted('firewall-rule-stored')
            ->assertSet('name', null)
            ->assertSet('port', null)
            ->assertSet('from_ip', null);
    }
}
