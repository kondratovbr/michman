<?php

namespace Tests\Feature\Webhooks;

use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Jobs\Exceptions\WrongWebhookCallTypeException;
use App\Jobs\Webhooks\HandlePingWebhookJob;
use App\Models\Webhook;
use App\Models\WebhookCall;
use App\States\Webhooks\Enabled;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class HandlePingWebhookJobTest extends AbstractFeatureTest
{
    public function test_call_gets_handled()
    {
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->withProject()
            ->inState('enabling')
            ->create();
        /** @var WebhookCall $call */
        $call = WebhookCall::factory([
            'type' => 'ping',
            'processed' => false,
        ])
            ->for($hook)
            ->create();

        $job = new HandlePingWebhookJob($call);

        $this->assertEquals('default', $job->queue);

        Bus::fake();
        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'state' => 'enabled',
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => true,
        ]);

        $hook->refresh();
        $call->refresh();

        $this->assertTrue($hook->exists);
        $this->assertTrue($hook->state->is(Enabled::class));

        $this->assertTrue($call->exists);
        $this->assertTrue($call->processed);

        Event::assertDispatched(WebhookUpdatedEvent::class);
    }

    /** @dataProvider irrelevantStates */
    public function test_irrelevant_webhooks_get_ignored(string $state)
    {
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->withProject()
            ->inState($state)
            ->create();
        /** @var WebhookCall $call */
        $call = WebhookCall::factory([
            'type' => 'ping',
            'processed' => false,
        ])
            ->for($hook)
            ->create();

        $job = new HandlePingWebhookJob($call);

        Bus::fake();
        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'state' => $state,
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => true,
        ]);

        $hook->refresh();
        $call->refresh();

        $this->assertTrue($hook->exists);
        $this->assertEquals($state, $hook->state->getValue());

        $this->assertTrue($call->exists);
        $this->assertTrue($call->processed);

        Event::assertNotDispatched(WebhookUpdatedEvent::class);
    }

    public function test_call_type_gets_verified()
    {
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->withProject()
            ->inState('enabling')
            ->create();
        /** @var WebhookCall $call */
        $call = WebhookCall::factory([
            'type' => 'push',
            'processed' => false,
        ])
            ->for($hook)
            ->create();

        $job = new HandlePingWebhookJob($call);

        Bus::fake();
        Event::fake();

        $this->expectException(WrongWebhookCallTypeException::class);

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'state' => 'enabling',
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => false,
        ]);

        Event::assertNotDispatched(WebhookUpdatedEvent::class);
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
            'type' => 'ping',
            'processed' => false,
        ])
            ->for($hook)
            ->create();

        $job = new HandlePingWebhookJob($call);

        Bus::fake();
        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'state' => 'deleting',
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $call->id,
            'processed' => true,
        ]);

        Event::assertNotDispatched(WebhookUpdatedEvent::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['deleting'],
            ['enabled'],
        ];
    }
}
