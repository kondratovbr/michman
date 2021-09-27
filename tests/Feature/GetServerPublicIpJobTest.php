<?php

namespace Tests\Feature;

use App\Jobs\Servers\GetServerPublicIpJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Services\ServerProviderInterface;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class GetServerPublicIpJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Server $server */
        $server = Server::factory([
            'external_id' => '100500',
        ])
            ->has(WorkerSshKey::factory(), 'workerSshKey')
            ->for(Provider::factory()->digitalOceanV2()->withOwner(), 'provider')
            ->create();

        $job = new GetServerPublicIpJob($server);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('digital_ocean_v2_servers', ServerProviderInterface::class,
            function (MockInterface $mock) use ($server) {
                $mock->shouldReceive('getServerPublicIp4')
                    ->with($server->externalId)
                    ->once()
                    ->andReturn('192.168.1.1');
            }
        );

        app()->call([$job, 'handle']);

        $server->refresh();

        $this->assertEquals('192.168.1.1', $server->publicIp);
    }
}
