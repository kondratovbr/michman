<?php

namespace Tests\Feature;

use App\DataTransferObjects\SshKeyData;
use App\Jobs\Providers\AddServerSshKeyToProviderJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Services\ServerProviderInterface;
use App\Support\Str;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class AddServerSshKeyToProviderJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->for(Provider::factory()->digitalOceanV2())
            ->create();
        /** @var WorkerSshKey $workerSshKey */
        $workerSshKey = WorkerSshKey::factory()->for($server)->create();

        $job = new AddServerSshKeyToProviderJob($server);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('digital_ocean_v2', ServerProviderInterface::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('addSshKeySafely')
                ->with($server->workerSshKey->name, $server->workerSshKey->publicKeyString)
                ->once()
                ->andReturn(new SshKeyData(
                    id: '100500',
                    fingerprint: Str::random(),
                    publicKey: $server->workerSshKey->publicKeyString,
                    name: $server->workerSshKey->name,
                ));
        });

        app()->call([$job, 'handle']);

        $workerSshKey->fresh();

        $this->assertEquals('100500', $workerSshKey->externalId);

        // TODO: CRITICAL! CONTINUE! Make sure it works, right now it doesn't.
    }
}
