<?php

namespace Tests\Feature;

use App\Jobs\Webhooks\HandlePingWebhookJob;
use App\Models\Project;
use App\Models\VcsProvider;
use App\Models\Webhook;
use App\Models\WebhookCall;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

// TODO: CRITICAL! CONTINUE. Create an actual webhook and check out the GitHub's "Deliveries" page to see the actual headers to put here.

class WebhookControllerTest extends AbstractFeatureTest
{
    public function test_valid_webhook_call_gets_accepted()
    {
        $hook = $this->webhook();

        Bus::fake();

        $response = $this->post(
            'hook/github/' . $hook->uuid,
            [
                'zen' => 'Bullshit meaningless quote.',
                'hook_id' => 123,
                'hook' => [
                    'type' => 'Repository',
                    'id' => 123,
                    'name' => 'web',
                    'active' => true,
                    'events' => [
                        '*',
                    ],
                    'config' => [
                        'content_type' => 'json',
                        'url' => 'http://localhost/****************',
                        'insecure_ssl' => 1,
                    ],
                ],
            ],
            [
                //
            ],
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('webhook_calls', [
            'webhook_id' => $hook->id,
            'type' => 'ping',
            'url' => 'http://localhost/hook/github' . $hook->uuid,
            'external_id' => '5',
            'processed' => false,
        ]);

        $hook->refresh();

        $this->assertCount(1, $hook->calls);

        /** @var WebhookCall $call */
        $call = $hook->calls->first();

        $this->assertEquals('ping', $call->type);
        $this->assertFalse($call->processed);
        //

        Bus::assertDispatched(HandlePingWebhookJob::class);
    }

    public function test_invalid_provider_is_caught()
    {
        //
    }

    public function test_invalid_event_is_caught()
    {
        //
    }

    public function test_invalid_id_is_caught()
    {
        //
    }

    public function test_invalid_signature_is_caught()
    {
        //
    }

    protected function webhook(): Webhook
    {
        /** @var Project $project */
        $project = Project::factory()
            ->withUserAndServers()
            ->repoInstalled()
            ->create();

        /** @var VcsProvider $vcs */
        $vcs = VcsProvider::factory([
            'provider' => 'github_v3',
        ])
            ->for($project->user)
            ->for($project)
            ->create();

        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'provider' => 'github',
            'url' => route('hook.push', [$hook->provider, $hook]),
            'external_id' => '123',
        ])
            ->for($project)
            ->create();

        return $hook;
    }
}
