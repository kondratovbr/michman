<?php

namespace Tests\Feature\Webhooks;

use App\Actions\Webhooks\CreateProjectWebhookAction;
use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Jobs\Webhooks\EnableWebhookJob;
use App\Models\Project;
use App\Models\VcsProvider;
use App\Models\Webhook;
use App\States\Webhooks\Enabling;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class CreateProjectWebhookActionTest extends AbstractFeatureTest
{
    public function test_webhook_gets_created()
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory(['provider' => 'github_v3'])->withUser()->create();
        /** @var Project $project */
        $project = Project::factory()
            ->for($vcsProvider)
            ->for($vcsProvider->user)
            ->repoInstalled()
            ->create();

        /** @var CreateProjectWebhookAction $action */
        $action = $this->app->make(CreateProjectWebhookAction::class);

        Bus::fake();
        Event::fake(WebhookCreatedEvent::class);

        $action->execute($project);

        $project->refresh();

        $this->assertNotNull($project->webhook);

        $hook = $project->webhook;

        $this->assertEquals('github', $hook->provider);
        $this->assertEquals('push', $hook->type);
        $this->assertNotNull($hook->secret);
        $this->assertTrue($hook->state->is(Enabling::class));

        Bus::assertDispatched(EnableWebhookJob::class);
        Event::assertDispatched(WebhookCreatedEvent::class);
    }

    public function test_duplicated_webhook_does_not_get_created()
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory(['provider' => 'github_v3'])->withUser()->create();
        /** @var Project $project */
        $project = Project::factory()
            ->for($vcsProvider)
            ->for($vcsProvider->user)
            ->repoInstalled()
            ->create();
        /** @var Webhook $hook */
        $hook = $project->webhook()->create([
            'type' => 'push',
            'provider' => 'github',
            'secret' => Str::random(40),
            'state' => Enabling::class,
        ]);

        /** @var CreateProjectWebhookAction $action */
        $action = $this->app->make(CreateProjectWebhookAction::class);

        Bus::fake();
        Event::fake([
            WebhookCreatedEvent::class,
            WebhookUpdatedEvent::class,
            WebhookDeletedEvent::class,
        ]);

        $action->execute($project);

        $project->refresh();

        $this->assertNotNull($project->webhook);

        Bus::assertNotDispatched(EnableWebhookJob::class);
        Event::assertNotDispatched(WebhookCreatedEvent::class);
        Event::assertNotDispatched(WebhookUpdatedEvent::class);
        Event::assertNotDispatched(WebhookDeletedEvent::class);
    }
}
