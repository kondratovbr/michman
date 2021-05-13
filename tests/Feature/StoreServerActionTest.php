<?php

namespace Tests\Feature;

use App\Actions\Servers\StoreServerAction;
use App\DataTransferObjects\NewServerData;
use App\Jobs\Providers\AddServerSshKeyToProviderJob;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\Servers\VerifyRemoteServerIsSuitableJob;
use App\Jobs\Servers\UpdateServerAvailabilityJob;
use App\Models\Provider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Tests\AbstractFeatureTest;

class StoreServerActionTest extends AbstractFeatureTest
{
    public function test_server_gets_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->owner;

        /** @var StoreServerAction $action */
        $action = App::make(StoreServerAction::class);

        $data = new NewServerData(
            provider: $provider,
            name: 'Server Name',
            region: 'region_1',
            size: 'size_1',
            type: 'app',
            pythonVersion: '3_9',
            database: 'mysql-8_0',
            dbName: 'app_db',
            cache: 'redis',
            addSshKeyToVcs: true,
        );

        Bus::fake();

        $server = $action->execute($data);

        $this->assertNotNull($server);
        $this->assertEquals($provider->id, $server->provider->id);
        $this->assertEquals('Server Name', $server->name);
        $this->assertEquals('app', $server->type);
        $this->assertNull($server->externalId);
        $this->assertNull($server->publicIp);
        $this->assertNull($server->sshHostKey);
        $this->assertEquals('22', $server->sshPort);
        $this->assertCount(1, $provider->servers);
        $this->assertDatabaseHas('servers', [
            'provider_id' => $provider->id,
            'name' => 'Server Name',
            'type' => 'app',
            'external_id' => null,
            'public_ip' => null,
            'ssh_port' => '22',
            'ssh_host_key' => null,
        ]);

        Bus::assertChained([
            CreateWorkerSshKeyForServerJob::class,
            AddServerSshKeyToProviderJob::class,
            RequestNewServerFromProviderJob::class,
            GetServerPublicIpJob::class,
            VerifyRemoteServerIsSuitableJob::class,
            PrepareRemoteServerJob::class,
            UpdateServerAvailabilityJob::class,
        ]);
    }
}
