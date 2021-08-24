<?php

namespace Tests\Feature\Firewall;

use App\Actions\Firewall\DeleteFirewallRuleAction;
use App\Events\Firewall\FirewallRuleUpdatedEvent;
use App\Jobs\FirewallRules\DeleteFirewallRuleJob;
use App\Models\FirewallRule;
use Tests\AbstractFeatureTest;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

class DeleteFirewallRuleActionTest extends AbstractFeatureTest
{
    public function test_status_update_and_job_dispatching()
    {
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory()->withServer()->create();

        Bus::fake();
        Event::fake();

        /** @var DeleteFirewallRuleAction $action */
        $action = $this->app->make(DeleteFirewallRuleAction::class);

        $action->execute($rule);

        $rule->refresh();

        $this->assertTrue($rule->isDeleting());
        $this->assertEquals(FirewallRule::STATUS_DELETING, $rule->status);

        Bus::assertDispatched(DeleteFirewallRuleJob::class);

        Event::assertDispatched(FirewallRuleUpdatedEvent::class);
    }
}
