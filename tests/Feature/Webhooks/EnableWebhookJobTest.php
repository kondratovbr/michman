<?php

namespace Tests\Feature\Webhooks;

use App\DataTransferObjects\WebhookDto;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Jobs\Webhooks\EnableWebhookJob;
use App\Jobs\Webhooks\VerifyWebhookEnabledJob;
use App\Models\Project;
use App\Models\User;
use App\Models\VcsProvider;
use App\Models\Webhook;
use App\Models\WebhookCall;
use App\Notifications\Projects\WebhookEnablingFailedNotification;
use App\Services\VcsProviderInterface;
use App\States\Webhooks\Enabling;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use RuntimeException;

class EnableWebhookJobTest extends AbstractFeatureTest
{
    public function test_webhook_gets_enabled()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Project $project */
        $project = Project::factory()
            ->for($user)
            ->for(VcsProvider::factory()->for($user))
            ->repoInstalled()
            ->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory()
            ->for($project)
            ->inState('enabling')
            ->create();

        $job = new EnableWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldReceive('addWebhookSafelyPush')
                ->with($hook->repo, $hook->url, $hook->secret)
                ->once()
                ->andReturn(new WebhookDto(['push'], '12345', $hook->url));
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        app()->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'external_id' => '12345',
            'state' => 'enabling',
        ]);

        $hook->refresh();

        $this->assertTrue($hook->exists);
        $this->assertEquals('12345', $hook->externalId);
        $this->assertTrue($hook->state->is(Enabling::class));

        Bus::assertDispatched(VerifyWebhookEnabledJob::class);
        Event::assertDispatched(WebhookUpdatedEvent::class);
        Notification::assertNothingSent();
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_hooks_get_ignored(string $state)
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Project $project */
        $project = Project::factory()
            ->for($user)
            ->for(VcsProvider::factory()->for($user))
            ->repoInstalled()
            ->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'external_id' => '666',
        ])
            ->for($project)
            ->inState($state)
            ->create();

        $job = new EnableWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldNotHaveBeenCalled();
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        app()->call([$job, 'handle']);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'external_id' => '666',
            'state' => $state,
        ]);

        $hook->refresh();

        $this->assertTrue($hook->exists);
        $this->assertEquals('666', $hook->externalId);
        $this->assertEquals($state, $hook->state->getValue());

        Bus::assertNotDispatched(VerifyWebhookEnabledJob::class);
        Event::assertNotDispatched(WebhookUpdatedEvent::class);
        Notification::assertNothingSent();
    }

    public function ineligibleStates(): array
    {
        return [
            ['deleting'],
            ['enabled'],
        ];
    }

    public function test_failure_gets_handled()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Project $project */
        $project = Project::factory()
            ->for($user)
            ->for(VcsProvider::factory()->for($user))
            ->repoInstalled()
            ->create();
        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'external_id' => null,
        ])
            ->for($project)
            ->inState('enabling')
            ->create();
        /** @var Collection $calls */
        $calls = WebhookCall::factory()->for($hook)->count(3)->create();

        $job = new EnableWebhookJob($hook);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('github_v3_vcs', VcsProviderInterface::class, function (MockInterface $mock) use ($hook) {
            $mock->shouldReceive('addWebhookSafelyPush')
                ->with($hook->repo, $hook->url, $hook->secret)
                ->once()
                ->andThrow(new RuntimeException);
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        $caught = false;
        try {
            app()->call([$job, 'handle']);
        } catch (RuntimeException) {
            $caught = true;
        }

        $this->assertTrue($caught);

        app()->call([$job, 'failed']);

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

        Bus::assertNotDispatched(VerifyWebhookEnabledJob::class);
        Event::assertDispatched(WebhookDeletedEvent::class);
        Event::assertNotDispatched(WebhookUpdatedEvent::class);
        Notification::assertSentTo($hook->user, WebhookEnablingFailedNotification::class);
    }
}
