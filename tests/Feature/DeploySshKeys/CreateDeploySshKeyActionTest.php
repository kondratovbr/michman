<?php

namespace Tests\Feature\DeploySshKeys;

use App\Actions\DeploySshKeys\CreateDeploySshKeyAction;
use App\Models\Project;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class CreateDeploySshKeyActionTest extends AbstractFeatureTest
{
    public function test_key_gets_created()
    {
        /** @var Project $project */
        $project = Project::factory([
            'domain' => 'foo.com',
        ])->withUserAndServers()->create();

        /** @var CreateDeploySshKeyAction $action */
        $action = $this->app->make(CreateDeploySshKeyAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($project);

        $this->assertDatabaseHas('deploy_ssh_keys', [
            'project_id' => $project->getKey(),
        ]);

        $project->refresh();

        $this->assertNotNull($project->deploySshKey);

        $key = $project->deploySshKey;

        $this->assertNotNull($key->privateKey);
        $this->assertNotNull($key->publicKey);
        $this->assertNotNull($key->privateKeyString);
        $this->assertNotNull($key->publicKeyString);
        $this->assertEquals('foo.com', $key->name);
        $this->assertStringContainsString('foo.com - deploy key', $key->publicKeyString);
    }
}
