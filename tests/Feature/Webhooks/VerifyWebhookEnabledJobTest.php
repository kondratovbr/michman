<?php

namespace Tests\Feature\Webhooks;

use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Jobs\Webhooks\VerifyWebhookEnabledJob;
use App\Models\Webhook;
use App\Models\WebhookCall;
use App\Notifications\Projects\WebhookEnablingFailedNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\AbstractFeatureTest;

class VerifyWebhookEnabledJobTest extends AbstractFeatureTest
{
    public function test_stuck_webhook_gets_deleted()
    {
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->withProject()
            ->inState('enabling')
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new VerifyWebhookEnabledJob($hook);

        $this->assertEquals('default', $job->queue);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Notification::assertSentTo($hook->user, WebhookEnablingFailedNotification::class);

        $this->assertDatabaseMissing('webhooks', [
            'id' => $hook->id,
        ]);
        $this->assertDatabaseMissing('webhook_calls', [
            'id' => $calls[0]->id,
        ]);
        $this->assertDatabaseMissing('webhook_calls', [
            'id' => $calls[1]->id,
        ]);
        $this->assertDatabaseMissing('webhook_calls', [
            'id' => $calls[2]->id,
        ]);

        Event::assertDispatched(WebhookDeletedEvent::class);
    }

    /** @dataProvider nonStuckStates */
    public function test_non_stuck_webhooks_get_ignored(string $state)
    {
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->withProject()
            ->inState($state)
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new VerifyWebhookEnabledJob($hook);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Notification::assertNothingSent();
        Event::assertNotDispatched(WebhookCreatedEvent::class);
        Event::assertNotDispatched(WebhookUpdatedEvent::class);
        Event::assertNotDispatched(WebhookDeletedEvent::class);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $calls[0]->id,
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $calls[1]->id,
        ]);
        $this->assertDatabaseHas('webhook_calls', [
            'id' => $calls[2]->id,
        ]);
    }

    public function nonStuckStates(): array
    {
        return [
            ['deleting'],
            ['enabled'],
        ];
    }
}
