<?php

namespace Tests\Feature\Firewall;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\Servers\AddFirewallRuleToServerJob;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreFirewallRuleActionTest extends AbstractFeatureTest
{
    public function test_firewall_rule_gets_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        $this->actingAs($user);

        /** @var StoreFirewallRuleAction $action */
        $action = $this->app->make(StoreFirewallRuleAction::class);

        Bus::fake();
        Event::fake();

        $rule = $action->execute(new FirewallRuleData(
            name: 'WHOIS',
            port: '43',
            from_ip: '127.0.0.1',
            can_delete: true,
        ), $server, false);

        $this->assertDatabaseHas('firewall_rules', [
            'id' => $rule->id,
            'name' => 'WHOIS',
            'port' => '43',
            'from_ip' => '127.0.0.1',
            'can_delete' => true,
            'status' => FirewallRule::STATUS_ADDING,
        ]);

        Bus::assertDispatched(AddFirewallRuleToServerJob::class);
    }
}
