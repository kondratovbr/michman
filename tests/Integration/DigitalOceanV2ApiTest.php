<?php

namespace Tests\Integration;

use App\DataTransferObjects\NewServerData;
use App\Models\Provider;
use App\Models\User;
use App\Services\ServerProviderInterface;
use App\Support\Str;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Tests\AbstractIntegrationTest;
use Mockery\MockInterface;
use Mockery;
use Closure;

class DigitalOceanV2ApiTest extends AbstractIntegrationTest
{
    private const BASE_PATH = 'https://api.digitalocean.com/v2';

    private function mockRequest(
        string $token,
        string $method,
        string $path,
        array $parameters,
        string|array|Closure $response
    ): void {
        Http::shouldReceive('withToken')
            ->with($token)
            ->once()
            ->andReturn(Mockery::mock(PendingRequest::class,
                function (MockInterface $mock) use ($method, $path, $parameters, $response) {
                    $mock->shouldReceive('acceptJson')
                        ->once()
                        ->andReturnSelf();
                    $mock->shouldReceive($method)
                        ->with(self::BASE_PATH . $path, $parameters)
                        ->once()
                        ->andReturn($this->mockResponse($response));
                }
            ));
    }

    private function mockResponse(string|array|Closure $response): MockInterface
    {
        if (is_callable($response))
            return Mockery::mock(Response::class, $response);

        $body = is_array($response)
            ? json_encode($response)
            : $response;

        return Mockery::mock(Response::class,
            function (MockInterface $mock) use ($body) {
                $mock->shouldReceive('throw')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('body')
                    ->once()
                    ->andReturn($body);
            }
        );
    }

    public function test_valid_token_can_be_validated()
    {
        $token = Str::random();

        $this->mockRequest($token, 'get', '/account', [],
            function (MockInterface $response) {
                $response->shouldReceive('throw')->once()->andReturnSelf();
                $response->shouldReceive('successful')->once()->andReturnTrue();
            }
        );

        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2', ['token' => $token]);

        $this->assertTrue($api->credentialsAreValid());
    }

    public function test_invalid_token_cannot_be_validated()
    {
        $token = Str::random();

        $this->mockRequest($token, 'get', '/account', [],
            function (MockInterface $mock) {
                $mock->shouldReceive('throw')->once()->andReturnSelf();
                $mock->shouldReceive('successful')->once()->andReturnFalse();
            }
        );

        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2', ['token' => $token]);

        $this->assertFalse($api->credentialsAreValid());
    }

    public function test_get_server()
    {
        $token = Str::random();
        $serverId = '234366208';

        $this->mockRequest($token, 'get', '/droplets/' . $serverId, [],
            '{"droplet":{"id":234366208,"name":"server-app-1","memory":1024,"vcpus":1,"disk":25,"locked":false,"status":"active","kernel":null,"created_at":"2021-03-01T14:20:27Z","features":["backups","monitoring","private_networking","ipv6"],"backup_ids":[82499878,82923703,83347020],"next_backup_window":{"start":"2021-05-08T20:00:00Z","end":"2021-05-09T19:00:00Z"},"snapshot_ids":[],"image":{"id":72067660,"name":"20.04 (LTS) x64","distribution":"Ubuntu","slug":"ubuntu-20-04-x64","public":true,"regions":["nyc3","nyc1","sfo1","nyc2","ams2","sgp1","lon1","ams3","fra1","tor1","sfo2","blr1","sfo3"],"created_at":"2020-10-20T16:34:30Z","min_disk_size":15,"type":"base","size_gigabytes":0.52,"description":"Ubuntu 20.04 x86","tags":[],"status":"available"},"volume_ids":[],"size":{"slug":"s-1vcpu-1gb","memory":1024,"vcpus":1,"disk":25,"transfer":1.0,"price_monthly":5.0,"price_hourly":0.00744,"regions":["ams2","ams3","blr1","fra1","lon1","nyc1","nyc2","nyc3","sfo1","sfo3","sgp1","tor1"],"available":true,"description":"Basic"},"size_slug":"s-1vcpu-1gb","networks":{"v4":[{"ip_address":"10.106.0.2","netmask":"255.255.240.0","gateway":"","type":"private"},{"ip_address":"46.101.41.160","netmask":"255.255.192.0","gateway":"46.101.0.1","type":"public"}],"v6":[{"ip_address":"2a03:b0c0:1:d0::ccd:6001","netmask":64,"gateway":"2a03:b0c0:1:d0::1","type":"public"}]},"region":{"name":"London 1","slug":"lon1","features":["backups","ipv6","metadata","install_agent","storage","image_transfer"],"available":true,"sizes":["s-1vcpu-1gb","s-1vcpu-1gb-amd","s-1vcpu-1gb-intel","s-1vcpu-2gb","s-1vcpu-2gb-amd","s-1vcpu-2gb-intel","s-2vcpu-2gb","s-2vcpu-2gb-amd","s-2vcpu-2gb-intel","s-2vcpu-4gb","s-2vcpu-4gb-amd","s-2vcpu-4gb-intel","s-4vcpu-8gb","c-2","c2-2vcpu-4gb","s-4vcpu-8gb-amd","s-4vcpu-8gb-intel","g-2vcpu-8gb","gd-2vcpu-8gb","s-8vcpu-16gb","m-2vcpu-16gb","c-4","c2-4vcpu-8gb","s-8vcpu-16gb-amd","s-8vcpu-16gb-intel","m3-2vcpu-16gb","g-4vcpu-16gb","so-2vcpu-16gb","m6-2vcpu-16gb","gd-4vcpu-16gb","so1_5-2vcpu-16gb","m-4vcpu-32gb","c-8","c2-8vcpu-16gb","m3-4vcpu-32gb","g-8vcpu-32gb","so-4vcpu-32gb","m6-4vcpu-32gb","gd-8vcpu-32gb","so1_5-4vcpu-32gb","m-8vcpu-64gb","c-16","c2-16vcpu-32gb","m3-8vcpu-64gb","g-16vcpu-64gb","so-8vcpu-64gb","m6-8vcpu-64gb","gd-16vcpu-64gb","so1_5-8vcpu-64gb","m-16vcpu-128gb","c-32","c2-32vcpu-64gb","m3-16vcpu-128gb","m-24vcpu-192gb","g-32vcpu-128gb","so-16vcpu-128gb","m6-16vcpu-128gb","gd-32vcpu-128gb","m3-24vcpu-192gb","g-40vcpu-160gb","so1_5-16vcpu-128gb","m-32vcpu-256gb","gd-40vcpu-160gb","so-24vcpu-192gb","m6-24vcpu-192gb","m3-32vcpu-256gb","so1_5-24vcpu-192gb","so-32vcpu-256gb","m6-32vcpu-256gb"]},"tags":[],"vpc_uuid":"a3471ec6-7bd0-41ff-8c29-e7650dc29693"}}'
        );

        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2', ['token' => $token]);

        $server = $api->getServer($serverId);

        $this->assertEquals($serverId, $server->id);
        $this->assertEquals('server-app-1', $server->name);
        $this->assertEquals('46.101.41.160', $server->publicIp4);
    }

    public function test_get_all_servers()
    {
        $token = Str::random();

        $this->mockRequest($token, 'get', '/droplets', [],
            '{"droplets":[{"id":234366208,"name":"server-app-1","memory":1024,"vcpus":1,"disk":25,"locked":false,"status":"active","kernel":null,"created_at":"2021-03-01T14:20:27Z","features":["backups","monitoring","private_networking","ipv6"],"backup_ids":[82499878,82923703,83347020],"next_backup_window":{"start":"2021-05-08T20:00:00Z","end":"2021-05-09T19:00:00Z"},"snapshot_ids":[],"image":{"id":72067660,"name":"20.04 (LTS) x64","distribution":"Ubuntu","slug":"ubuntu-20-04-x64","public":true,"regions":["nyc3","nyc1","sfo1","nyc2","ams2","sgp1","lon1","ams3","fra1","tor1","sfo2","blr1","sfo3"],"created_at":"2020-10-20T16:34:30Z","min_disk_size":15,"type":"base","size_gigabytes":0.52,"description":"Ubuntu 20.04 x86","tags":[],"status":"available"},"volume_ids":[],"size":{"slug":"s-1vcpu-1gb","memory":1024,"vcpus":1,"disk":25,"transfer":1.0,"price_monthly":5.0,"price_hourly":0.00744,"regions":["ams2","ams3","blr1","fra1","lon1","nyc1","nyc2","nyc3","sfo1","sfo3","sgp1","tor1"],"available":true,"description":"Basic"},"size_slug":"s-1vcpu-1gb","networks":{"v4":[{"ip_address":"10.106.0.2","netmask":"255.255.240.0","gateway":"","type":"private"},{"ip_address":"46.101.41.160","netmask":"255.255.192.0","gateway":"46.101.0.1","type":"public"}],"v6":[{"ip_address":"2a03:b0c0:1:d0::ccd:6001","netmask":64,"gateway":"2a03:b0c0:1:d0::1","type":"public"}]},"region":{"name":"London 1","slug":"lon1","features":["backups","ipv6","metadata","install_agent","storage","image_transfer"],"available":true,"sizes":["s-1vcpu-1gb","s-1vcpu-1gb-amd","s-1vcpu-1gb-intel","s-1vcpu-2gb","s-1vcpu-2gb-amd","s-1vcpu-2gb-intel","s-2vcpu-2gb","s-2vcpu-2gb-amd","s-2vcpu-2gb-intel","s-2vcpu-4gb","s-2vcpu-4gb-amd","s-2vcpu-4gb-intel","s-4vcpu-8gb","c-2","c2-2vcpu-4gb","s-4vcpu-8gb-amd","s-4vcpu-8gb-intel","g-2vcpu-8gb","gd-2vcpu-8gb","s-8vcpu-16gb","m-2vcpu-16gb","c-4","c2-4vcpu-8gb","s-8vcpu-16gb-amd","s-8vcpu-16gb-intel","m3-2vcpu-16gb","g-4vcpu-16gb","so-2vcpu-16gb","m6-2vcpu-16gb","gd-4vcpu-16gb","so1_5-2vcpu-16gb","m-4vcpu-32gb","c-8","c2-8vcpu-16gb","m3-4vcpu-32gb","g-8vcpu-32gb","so-4vcpu-32gb","m6-4vcpu-32gb","gd-8vcpu-32gb","so1_5-4vcpu-32gb","m-8vcpu-64gb","c-16","c2-16vcpu-32gb","m3-8vcpu-64gb","g-16vcpu-64gb","so-8vcpu-64gb","m6-8vcpu-64gb","gd-16vcpu-64gb","so1_5-8vcpu-64gb","m-16vcpu-128gb","c-32","c2-32vcpu-64gb","m3-16vcpu-128gb","m-24vcpu-192gb","g-32vcpu-128gb","so-16vcpu-128gb","m6-16vcpu-128gb","gd-32vcpu-128gb","m3-24vcpu-192gb","g-40vcpu-160gb","so1_5-16vcpu-128gb","m-32vcpu-256gb","gd-40vcpu-160gb","so-24vcpu-192gb","m6-24vcpu-192gb","m3-32vcpu-256gb","so1_5-24vcpu-192gb","so-32vcpu-256gb","m6-32vcpu-256gb"]},"tags":[],"vpc_uuid":"a3471ec6-7bd0-41ff-8c29-e7650dc29693"}],"links":{},"meta":{"total":1}}'
        );

        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2', ['token' => $token]);

        $servers = $api->getAllServers();

        $this->assertCount(1, $servers);
        $this->assertEquals('234366208', $servers->first()->id);
        $this->assertEquals('server-app-1', $servers->first()->name);
        $this->assertEquals('46.101.41.160', $servers->first()->publicIp4);
    }

    public function test_get_server_public_ipv4()
    {
        $token = Str::random();
        $serverId = '234366208';

        $this->mockRequest($token, 'get', '/droplets/' . $serverId, [],
            '{"droplet":{"id":234366208,"name":"server-app-1","memory":1024,"vcpus":1,"disk":25,"locked":false,"status":"active","kernel":null,"created_at":"2021-03-01T14:20:27Z","features":["backups","monitoring","private_networking","ipv6"],"backup_ids":[82499878,82923703,83347020],"next_backup_window":{"start":"2021-05-08T20:00:00Z","end":"2021-05-09T19:00:00Z"},"snapshot_ids":[],"image":{"id":72067660,"name":"20.04 (LTS) x64","distribution":"Ubuntu","slug":"ubuntu-20-04-x64","public":true,"regions":["nyc3","nyc1","sfo1","nyc2","ams2","sgp1","lon1","ams3","fra1","tor1","sfo2","blr1","sfo3"],"created_at":"2020-10-20T16:34:30Z","min_disk_size":15,"type":"base","size_gigabytes":0.52,"description":"Ubuntu 20.04 x86","tags":[],"status":"available"},"volume_ids":[],"size":{"slug":"s-1vcpu-1gb","memory":1024,"vcpus":1,"disk":25,"transfer":1.0,"price_monthly":5.0,"price_hourly":0.00744,"regions":["ams2","ams3","blr1","fra1","lon1","nyc1","nyc2","nyc3","sfo1","sfo3","sgp1","tor1"],"available":true,"description":"Basic"},"size_slug":"s-1vcpu-1gb","networks":{"v4":[{"ip_address":"10.106.0.2","netmask":"255.255.240.0","gateway":"","type":"private"},{"ip_address":"46.101.41.160","netmask":"255.255.192.0","gateway":"46.101.0.1","type":"public"}],"v6":[{"ip_address":"2a03:b0c0:1:d0::ccd:6001","netmask":64,"gateway":"2a03:b0c0:1:d0::1","type":"public"}]},"region":{"name":"London 1","slug":"lon1","features":["backups","ipv6","metadata","install_agent","storage","image_transfer"],"available":true,"sizes":["s-1vcpu-1gb","s-1vcpu-1gb-amd","s-1vcpu-1gb-intel","s-1vcpu-2gb","s-1vcpu-2gb-amd","s-1vcpu-2gb-intel","s-2vcpu-2gb","s-2vcpu-2gb-amd","s-2vcpu-2gb-intel","s-2vcpu-4gb","s-2vcpu-4gb-amd","s-2vcpu-4gb-intel","s-4vcpu-8gb","c-2","c2-2vcpu-4gb","s-4vcpu-8gb-amd","s-4vcpu-8gb-intel","g-2vcpu-8gb","gd-2vcpu-8gb","s-8vcpu-16gb","m-2vcpu-16gb","c-4","c2-4vcpu-8gb","s-8vcpu-16gb-amd","s-8vcpu-16gb-intel","m3-2vcpu-16gb","g-4vcpu-16gb","so-2vcpu-16gb","m6-2vcpu-16gb","gd-4vcpu-16gb","so1_5-2vcpu-16gb","m-4vcpu-32gb","c-8","c2-8vcpu-16gb","m3-4vcpu-32gb","g-8vcpu-32gb","so-4vcpu-32gb","m6-4vcpu-32gb","gd-8vcpu-32gb","so1_5-4vcpu-32gb","m-8vcpu-64gb","c-16","c2-16vcpu-32gb","m3-8vcpu-64gb","g-16vcpu-64gb","so-8vcpu-64gb","m6-8vcpu-64gb","gd-16vcpu-64gb","so1_5-8vcpu-64gb","m-16vcpu-128gb","c-32","c2-32vcpu-64gb","m3-16vcpu-128gb","m-24vcpu-192gb","g-32vcpu-128gb","so-16vcpu-128gb","m6-16vcpu-128gb","gd-32vcpu-128gb","m3-24vcpu-192gb","g-40vcpu-160gb","so1_5-16vcpu-128gb","m-32vcpu-256gb","gd-40vcpu-160gb","so-24vcpu-192gb","m6-24vcpu-192gb","m3-32vcpu-256gb","so1_5-24vcpu-192gb","so-32vcpu-256gb","m6-32vcpu-256gb"]},"tags":[],"vpc_uuid":"a3471ec6-7bd0-41ff-8c29-e7650dc29693"}}'
        );

        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2', ['token' => $token]);

        $address = $api->getServerPublicIp4($serverId);

        $this->assertEquals('46.101.41.160', $address);
    }

    public function test_create_server()
    {
        $provider = Provider::factory()->for(
            User::factory()->withPersonalTeam(),
            'owner'
        )->create();
        $serverData = new NewServerData(
            provider: $provider,
            name: 'new-server',
            region: 'nyc1',
            size: 'size-1',
            type: 'app',
        );
        $token = Str::random();

        $this->mockRequest($token, 'post', '/droplets',
            [
                'name' => $serverData->name,
                'region' => $serverData->region,
                'size' => $serverData->size,
                'image' => (string) config('providers.list.digital_ocean_v2.default_image'),
                'ssh_keys' => [1],
                'monitoring' => true,
            ],
            '{"droplet": {"id": 666,"name": "new-server","memory": 1024,"vcpus": 1,"disk": 25,"locked": true,"status": "new","kernel": {"id": 2233,"name": "Ubuntu 14.04 x64 vmlinuz-3.13.0-37-generic","version": "3.13.0-37-generic"},"created_at": "2014-11-14T16:36:31Z","features": ["virtio"],"backup_ids": [],"snapshot_ids": [],"image": {},"volume_ids": [],"size": {},"size_slug": "size-1","networks": {},"region": {},"tags": ["web"]},"links": {"actions": [{"id": 101,"rel": "create","href": "https://api.digitalocean.com/v2/actions/36805096"}]}}'
        );

        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2', ['token' => $token]);

        $createdServer = $api->createServer($serverData, 1);

        $this->assertEquals('666', $createdServer->id);
        $this->assertEquals('new-server', $createdServer->name);
    }

    public function test_get_all_regions()
    {
        //
    }
}
