<?php

namespace Tests\Feature;

use App\DataTransferObjects\SshKeyData;
use App\Jobs\Servers\AddWorkerSshKeyToServerProviderJob;
use App\Models\WorkerSshKey;
use App\Services\ServerProviderInterface;
use App\Support\Str;
use Mockery\MockInterface;
use phpseclib3\Crypt\EC;
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
                        $server->name,
                        trim($key->getPublicKey()->toString('OpenSSH', ['comment' => ''])),
                    )
                    ->once()
                    ->andReturn(new SshKeyData(
                        id: '100500',
                        fingerprint: Str::random(),
                        publicKey: trim($key->getPublicKey()->toString('OpenSSH', ['comment' => ''])),
                        name: $server->name,
                    ));
            }
        );

        app()->call([$job, 'handle']);

        $workerSshKey->refresh();

        $this->assertEquals('100500', $workerSshKey->externalId);
    }
}
