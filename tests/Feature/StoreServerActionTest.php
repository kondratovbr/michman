<?php

namespace Tests\Feature;

use App\Actions\Servers\StoreServerAction;
use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\DataTransferObjects\NewServerDto;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerProviderJob;
use App\Jobs\Servers\ConfigureAppServerJob;
use App\Jobs\ServerSshKeys\CreateServerSshKeyJob;
use App\Jobs\Servers\GetServerPublicIpJob;
use App\Jobs\Servers\PrepareRemoteServerJob;
use App\Jobs\Servers\RequestNewServerFromProviderJob;
use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Jobs\Servers\VerifyRemoteServerIsSuitableJob;
use App\Jobs\Servers\UpdateServerAvailabilityJob;
use App\Models\Provider;
use App\Models\Server;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Bus;
use Tests\AbstractFeatureTest;

class StoreServerActionTest extends AbstractFeatureTest
{
    public function test_server_gets_created()
    {
        /** @var Provider $provider */
        $provider = Provider::factory()->withOwner()->create();
        $user = $provider->owner;
        VcsProvider::factory([
            'provider' => 'github_v3',
        ])->for($user)->create();
        VcsProvider::factory([
            'provider' => 'gitlab',
        ])->for($user)->create();

        $spy = $this->spy(CreateWorkerSshKeyAction::class);

        /** @var StoreServerAction $action */
        $action = $this->app->make(StoreServerAction::class);

        $data = new NewServerDto(
            name: 'Server Name',
            region: 'region_1',
            size: 'size_1',
            type: 'app',
            python_version: '3_9',
            database: 'mysql-8_0',
            cache: 'redis',
        );

        Bus::fake();

        $server = $action->execute($data, $provider);

        $spy->shouldHaveReceived('execute')
            ->withArgs(fn(Server $serverArg) => $serverArg->is($server))
            ->once();

        $this->assertNotNull($server);
        $this->assertEquals($provider->id, $server->provider->id);
        $this->assertEquals('Server Name', $server->name);
        $this->assertEquals('app', $server->type);
        $this->assertNull($server->externalId);
        $this->assertNull($server->publicIp);
        $this->assertNull($server->sshHostKey);
        $this->assertEquals('22', $server->sshPort);
        $this->assertNotNull($server->sudoPassword);
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
            AddWorkerSshKeyToServerProviderJob::class,
            RequestNewServerFromProviderJob::class,
            GetServerPublicIpJob::class,
            VerifyRemoteServerIsSuitableJob::class,
            PrepareRemoteServerJob::class,
            UpdateServerAvailabilityJob::class,
            AddWorkerSshKeyToServerJob::class,
            CreateServerSshKeyJob::class,
            UploadServerSshKeyToServerJob::class,
            ConfigureAppServerJob::class,
        ]);
    }
}
