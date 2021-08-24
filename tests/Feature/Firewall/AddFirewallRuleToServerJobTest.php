<?php

namespace Tests\Feature\Firewall;

use App\Jobs\FirewallRules\AddFirewallRuleToServerJob;
use App\Models\FirewallRule;
use App\Models\Server;
use App\Scripts\Root\AddFirewallRuleScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class AddFirewallRuleToServerJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory([
            'from_ip' => '127.0.0.1',
            'status' => FirewallRule::STATUS_ADDING,
        ])->withServer()->create();

        Bus::fake();
        Event::fake();

        $job = new AddFirewallRuleToServerJob($rule);

        $this->assertEquals('servers', $job->queue);

        $this->mock(AddFirewallRuleScript::class, function (MockInterface $mock) use ($rule) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $portArg,
                    bool $limitArg,
                    string $fromIpArg,
                ) use ($rule) {
                    return $serverArg->is($rule->server)
                        && $portArg === $rule->port
                        && $limitArg === false
                        && $fromIpArg === $rule->fromIp;
                })
                ->once();
        });

        app()->call([$job, 'handle']);

        $rule->refresh();

        $this->assertEquals(FirewallRule::STATUS_ADDED, $rule->status);
        $this->assertTrue($rule->isAdded());
    }
}
