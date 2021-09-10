<?php

namespace Tests\Feature;

use App\DataTransferObjects\SshKeyDto;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerProviderJob;
use App\Models\WorkerSshKey;
use App\Services\ServerProviderInterface;
use App\Support\SshKeyFormatter;
use App\Support\Str;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class AddWorkerSshKeyToProviderJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var WorkerSshKey $workerSshKey */
        $workerSshKey = WorkerSshKey::factory()->withServer()->create();
        $key = $workerSshKey->privateKey;
        $server = $workerSshKey->server;

        $job = new AddWorkerSshKeyToServerProviderJob($server);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('digital_ocean_v2', ServerProviderInterface::class,
            function (MockInterface $mock) use ($server, $key) {
                $mock->shouldReceive('addSshKeySafely')
                    ->with(
                        $server->name . ' - Michman worker key',
                        SshKeyFormatter::format($key->getPublicKey()),
                    )
                    ->once()
                    ->andReturn(new SshKeyDto(
                        id: '100500',
                        fingerprint: Str::random(),
                        publicKey: SshKeyFormatter::format($key->getPublicKey()),
                        name: $server->name . ' - Michman worker key',
                    ));
            }
        );

        app()->call([$job, 'handle']);

        $workerSshKey->refresh();

        $this->assertNotNull($workerSshKey);
        $this->assertEquals('100500', $workerSshKey->externalId);
        $this->assertEquals($server->name . ' - Michman worker key', $workerSshKey->name);
        $this->assertEquals(
            SshKeyFormatter::format($key->getPublicKey()),
            $workerSshKey->getPublicKeyString(false)
        );
    }
}
