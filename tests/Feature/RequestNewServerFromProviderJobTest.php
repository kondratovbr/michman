<?php

namespace Tests\Feature;

use App\DataTransferObjects\NewServerData;
use App\DataTransferObjects\ServerData;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Services\ServerProviderInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class RequestNewServerFromProviderJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->for(Provider::factory()->digitalOceanV2(), 'provider')
            ->has(WorkerSshKey::factory()->withRandomExternalId(), 'workerSshKey')
            ->create();

        $data = new NewServerData(
            provider: $server->provider,
            name: $server->name,
            region: 'region_1',
            size: 'size_1',
            type: 'app',
            pythonVersion: '3_9',
            database: 'mysql-8_0',
            dbName: 'db_app',
            cache: 'redis',
            addSshKeyToVcs: true,
        );

        $job = new RequestNewServerFromProviderJob($server, $data);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('digital_ocean_v2', ServerProviderInterface::class,
            function (MockInterface $mock) use ($data, $server) {
                $mock->shouldReceive('createServer')
                    ->with($data, $server->workerSshKey->externalId)
                    ->once()
                    ->andReturn(new ServerData(
                        id: '123',
                        name: $server->name,
                        publicIp4: null,
                    ));
            }
        );

        $job->handle();

        $server->refresh();

        $this->assertNotNull($server);
        $this->assertEquals('123', $server->externalId);
        $this->assertNull($server->publicIp);
        $this->assertNull($server->sshHostKey);
    }
}
