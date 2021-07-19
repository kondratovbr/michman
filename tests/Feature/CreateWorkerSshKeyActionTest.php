<?php

namespace Tests\Feature;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\Models\Server;
use Tests\AbstractFeatureTest;

class CreateWorkerSshKeyActionTest extends AbstractFeatureTest
{
    public function test_worker_ssh_key_gets_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        $action = $this->app->make(CreateWorkerSshKeyAction::class);

        $sshKey = $action->execute($server);

        $this->assertNotNull($sshKey);
        $this->assertNotNull($sshKey->id);
        $this->assertEquals($server->name . ' - Michman worker key', $sshKey->name);
        $this->assertNull($sshKey->externalId);
        $this->assertNotNull($sshKey->privateKey);
        $this->assertNotNull($sshKey->publicKey);

        $this->assertDatabaseHas('worker_ssh_keys', [
            'id' => $sshKey->id,
            'name' => $sshKey->name,
            'external_id' => null,
        ]);
    }
}
