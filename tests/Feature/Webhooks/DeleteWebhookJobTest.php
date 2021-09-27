<?php

namespace Tests\Feature\Webhooks;

use App\Events\Webhooks\WebhookDeletedEvent;
use App\Jobs\Webhooks\DeleteWebhookJob;
use App\Models\Project;
use App\Models\VcsProvider;
use App\Models\Webhook;
use App\Models\WebhookCall;
use App\Notifications\Projects\WebhookDeletingFailedNotification;
use App\Services\VcsProviderInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use RuntimeException;

class DeleteWebhookJobTest extends AbstractFeatureTest
{
    public function test_webhook_gets_deleted()
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
        ])->withUser()->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'external_id' => '12345',
        ])
            ->for(Project::factory()
                ->for($vcsProvider->user)
                ->for($vcsProvider)
                ->repoInstalled()
            )
            ->inState('deleting')
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new DeleteWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        Event::fake();
        Notification::fake();

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldReceive('deleteWebhookIfExistsPush')
                ->with($hook->repo, $hook->url)
                ->once();
        });

        $this->app->call([$job, 'handle']);

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
        Notification::assertNothingSent();
    }

    /** @dataProvider irrelevantStates */
    public function test_irrelevant_webhooks_get_ignored(string $state)
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
        ])->withUser()->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'external_id' => '12345',
        ])
            ->for(Project::factory()
                ->for($vcsProvider->user)
                ->for($vcsProvider)
                ->repoInstalled()
            )
            ->inState($state)
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new DeleteWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        Event::fake();
        Notification::fake();

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldNotHaveBeenCalled();
        });

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'state' => $state,
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

        Event::assertNotDispatched(WebhookDeletedEvent::class);
        Notification::assertNothingSent();
    }

    public function test_api_is_not_called_for_webhook_without_external_id()
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
        ])->withUser()->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'external_id' => null,
        ])
            ->for(Project::factory()
                ->for($vcsProvider->user)
                ->for($vcsProvider)
                ->repoInstalled()
            )
            ->inState('deleting')
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new DeleteWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        Event::fake();
        Notification::fake();

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldNotHaveBeenCalled();
        });

        $this->app->call([$job, 'handle']);

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
        Notification::assertNothingSent();
    }

    public function test_failure_gets_handled()
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
        ])->withUser()->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'external_id' => '12345',
        ])
            ->for(Project::factory()
                ->for($vcsProvider->user)
                ->for($vcsProvider)
                ->repoInstalled()
            )
            ->inState('deleting')
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new DeleteWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        Event::fake();
        Notification::fake();

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldReceive('deleteWebhookIfExistsPush')
                ->with($hook->repo, $hook->url)
                ->once()
                ->andThrow(new RuntimeException);
        });

        $caught = false;
        try {
            $this->app->call([$job, 'handle']);
        } catch (RuntimeException) {
            $caught = true;
        }

        $this->assertTrue($caught);

        $this->app->call([$job, 'failed']);

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
        Notification::assertSentTo($hook->user, WebhookDeletingFailedNotification::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['enabling'],
            ['enabled'],
        ];
    }
}
