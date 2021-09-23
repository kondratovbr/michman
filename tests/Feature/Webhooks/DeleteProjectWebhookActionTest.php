<?php

namespace Tests\Feature\Webhooks;

use App\Actions\Webhooks\DeleteProjectWebhookAction;
use App\Events\Webhooks\WebhookCreatedEvent;
use App\Events\Webhooks\WebhookDeletedEvent;
use App\Events\Webhooks\WebhookUpdatedEvent;
use App\Jobs\Webhooks\DeleteWebhookJob;
use App\Models\Project;
use App\Models\VcsProvider;
use App\Models\Webhook;
use App\States\Webhooks\Deleting;
use App\States\Webhooks\Enabled;
use App\States\Webhooks\Enabling;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class DeleteProjectWebhookActionTest extends AbstractFeatureTest
{
    public function test_job_gets_scheduled_and_state_transitioned()
    {
        $project = $this->setupProject();

        /** @var Webhook $hook */
        $hook = $project->webhook()->create([
            'type' => 'push',
            'provider' => 'github',
            'url' => 'http://localhost/',
            'secret' => Str::random(40),
            'state' => Enabled::class,
            'external_id' => '12345',
        ]);

        /** @var DeleteProjectWebhookAction $action */
        $action = $this->app->make(DeleteProjectWebhookAction::class);

        Bus::fake();
        Event::fake([
            WebhookCreatedEvent::class,
            WebhookUpdatedEvent::class,
            WebhookDeletedEvent::class,
        ]);

        $action->execute($hook);

        $this->assertTrue($hook->exists);
        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'uuid' => $hook->uuid,
            'state' => 'deleting',
        ]);

        $hook->refresh();

        $this->assertNotNull($hook);
        $this->assertTrue($hook->state->is(Deleting::class));

        Bus::assertDispatched(DeleteWebhookJob::class);
        Event::assertDispatched(WebhookUpdatedEvent::class);
        Event::assertNotDispatched(WebhookDeletedEvent::class);
    }

    public function test_webhook_in_enabling_state_is_processed()
    {
        $project = $this->setupProject();

        /** @var Webhook $hook */
        $hook = $project->webhook()->create([
            'type' => 'push',
            'provider' => 'github',
            'url' => 'http://localhost/',
            'secret' => Str::random(40),
            'state' => Enabling::class,
        ]);

        /** @var DeleteProjectWebhookAction $action */
        $action = $this->app->make(DeleteProjectWebhookAction::class);

        Bus::fake();
        Event::fake([
            WebhookCreatedEvent::class,
            WebhookUpdatedEvent::class,
            WebhookDeletedEvent::class,
        ]);

        $action->execute($hook);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'uuid' => $hook->uuid,
            'state' => 'deleting',
        ]);

        $hook->refresh();

        $this->assertTrue($hook->state->is(Deleting::class));

        Bus::assertDispatched(DeleteWebhookJob::class);
        Event::assertDispatched(WebhookUpdatedEvent::class);
        Event::assertNotDispatched(WebhookDeletedEvent::class);
    }

    public function test_webhook_in_deleting_state_is_ignored()
    {
        $project = $this->setupProject();

        /** @var Webhook $hook */
        $hook = $project->webhook()->create([
            'type' => 'push',
            'provider' => 'github',
            'url' => 'http://localhost/',
            'secret' => Str::random(40),
            'state' => Deleting::class,
        ]);

        /** @var DeleteProjectWebhookAction $action */
        $action = $this->app->make(DeleteProjectWebhookAction::class);

        Bus::fake();
        Event::fake([
            WebhookCreatedEvent::class,
            WebhookUpdatedEvent::class,
            WebhookDeletedEvent::class,
        ]);

        $action->execute($hook);

        $this->assertDatabaseHas('webhooks', [
            'id' => $hook->id,
            'uuid' => $hook->uuid,
            'state' => 'deleting',
        ]);

        $hook->refresh();

        $this->assertTrue($hook->state->is(Deleting::class));

        Bus::assertNotDispatched(DeleteWebhookJob::class);
        Event::assertNotDispatched(WebhookUpdatedEvent::class);
        Event::assertNotDispatched(WebhookDeletedEvent::class);
    }

    protected function setupProject(): Project
    {
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory(['provider' => 'github_v3'])->withUser()->create();
        /** @var Project $project */
        $project = Project::factory()
            ->for($vcsProvider)
            ->for($vcsProvider->user)
            ->repoInstalled()
            ->create();

        return $project;
    }
}
