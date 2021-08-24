<?php

namespace Tests\Feature\Firewall;

use App\Jobs\FirewallRules\DeleteFirewallRuleJob;
use App\Models\FirewallRule;
use App\Models\Server;
use App\Scripts\Root\DeleteFirewallRuleScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteFirewallRuleJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var FirewallRule $rule */
        $rule = FirewallRule::factory([
            'status' => FirewallRule::STATUS_DELETING,
        ])->withServer()->create();

        Bus::fake();
        Event::fake();

        $job = new DeleteFirewallRuleJob($rule);

        $this->assertEquals('servers', $job->queue);

        $this->mock(DeleteFirewallRuleScript::class, function (MockInterface $mock) use ($rule) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $portArg,
                    bool $limitArg,
                    string|null $fromIpArg,
                ) use ($rule) {
                    return $serverArg->is($rule->server)
                        && $portArg === $rule->port
                        && $limitArg === false
                        && $fromIpArg === $rule->fromIp;
                })
                ->once();
        });

        app()->call([$job, 'handle']);

        $this->assertDatabaseMissing('firewall_rules', [
            'id' => $rule->getKey(),
        ]);
    }
}
