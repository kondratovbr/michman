<?php

namespace Tests\Feature;

use App\DataTransferObjects\NewServerDto;
use App\DataTransferObjects\ServerDto;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Services\ServerProviderInterface;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class RequestNewServerFromProviderJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->for(Provider::factory()->digitalOceanV2()->withOwner(), 'provider')
            ->has(WorkerSshKey::factory()->withRandomExternalId(), 'workerSshKey')
            ->create();

        $data = new NewServerDto(
            name: $server->name,
            region: 'region_1',
            size: 'size_1',
            type: 'app',
            python_version: '3_9',
            database: 'mysql-8_0',
            cache: 'redis',
        );

        $job = new RequestNewServerFromProviderJob($server, $data);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('digital_ocean_v2_servers', ServerProviderInterface::class,
            function (MockInterface $mock) use ($data, $server) {
                $mock->shouldReceive('createServer')
                    ->with($data, $server->workerSshKey->externalId)
                    ->once()
                    ->andReturn(new ServerDto(
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
