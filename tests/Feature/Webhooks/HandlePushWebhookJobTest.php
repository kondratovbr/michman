<?php

namespace Tests\Feature\Webhooks;

use App\Actions\Projects\DeployProjectAction;
use App\Jobs\Exceptions\WrongWebhookCallTypeException;
use App\Jobs\Webhooks\HandlePushWebhookJob;
use App\Models\Project;
use App\Models\Webhook;
use App\Models\WebhookCall;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class HandlePushWebhookJobTest extends AbstractFeatureTest
{
    public function test_action_gets_executed()
    {
        /** @var WebhookCall $call */
        $call = WebhookCall::factory([
            'type' => 'push',
            'processed' => false,
            'payload' => [
                'after' => '1234567890',
            ],
        ])
            ->withWebhook()
            ->create();

        $job = new HandlePushWebhookJob($call);

        $this->assertEquals('default', $job->queue);

        $this->mock(DeployProjectAction::class, function (MockInterface $mock) use ($call) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Project $projectArg,
                    string $commitArg,
                ) use ($call) {
                    return $projectArg->is($call->webhook->project)
                        && $commitArg === '1234567890';
                })
                ->once();
        });

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => true,
        ]);

        $call->refresh();

        $this->assertTrue($call->exists);
        $this->assertTrue($call->processed);
    }

    public function test_call_type_gets_verified()
    {
        /** @var WebhookCall $call */
        $call = WebhookCall::factory([
            'type' => 'ping',
            'processed' => false,
            'payload' => [
                'after' => '1234567890',
            ],
        ])
            ->withWebhook()
            ->create();

        $job = new HandlePushWebhookJob($call);

        $this->assertEquals('default', $job->queue);

        $this->mock(DeployProjectAction::class, function (MockInterface $mock) use ($call) {
            $mock->shouldNotHaveBeenCalled();
        });

        $this->expectException(WrongWebhookCallTypeException::class);

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => false,
        ]);
    }

    public function test_call_of_deleting_hook_is_ignored()
    {
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->withProject()
            ->inState('deleting')
            ->create();

        /** @var WebhookCall $call */
        $call = WebhookCall::factory([
            'type' => 'push',
            'processed' => false,
            'payload' => [
                'after' => '1234567890',
            ],
        ])
            ->for($hook)
            ->create();

        $job = new HandlePushWebhookJob($call);

        $this->assertEquals('default', $job->queue);

        $this->mock(DeployProjectAction::class, function (MockInterface $mock) use ($call) {
            $mock->shouldNotHaveBeenCalled();
        });

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => true,
        ]);
    }
}
