<?php

namespace Tests\Feature\Providers;

use App\DataTransferObjects\AuthTokenDto;
use App\DataTransferObjects\NewServerDto;
use App\DataTransferObjects\RegionDto;
use App\DataTransferObjects\ServerDto;
use App\DataTransferObjects\SizeDto;
use App\DataTransferObjects\SshKeyDto;
use App\Services\DigitalOceanV2;
use App\Services\ServerProviderInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Tests\AbstractFeatureTest;

class DigitalOceanV2ApiNewTest extends AbstractFeatureTest
{
    public const TOKEN = '666666';

    public function test_credentialsAreValid_method()
    {
        Http::fake();

        $result = $this->api()->credentialsAreValid();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account'));

        $this->assertTrue($result);
    }

    public function test_credentialsAreValid_method_with_invalid_token()
    {
        Http::fake([
            '*' => Http::response(null, 403),
        ]);

        $result = $this->api()->credentialsAreValid();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account'));

        $this->assertFalse($result);
    }

    public function test_getAllRegions_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/regions*' => Http::sequence()
                ->push([
                    'regions' => [
                        [
                            'name' => 'New York 1',
                            'slug' => 'nyc1',
                            'available' => true,
                            'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/regions?page=2&per_page=1',
                            'last' => 'https://api.digitalocean.com/v2/regions?page=3&per_page=1',
                        ],
                    ],
                ])
                ->push([
                    'regions' => [
                        [
                            'name' => 'New York 2',
                            'slug' => 'nyc2',
                            'available' => true,
                            'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/regions?page=3&per_page=1',
                            'last' => 'https://api.digitalocean.com/v2/regions?page=3&per_page=1',
                            'prev' => 'https://api.digitalocean.com/v2/regions?page=1&per_page=1',
                            'first' => 'https://api.digitalocean.com/v2/regions?page=1&per_page=1',
                        ],
                    ],
                ])
                ->push([
                    'regions' => [
                        [
                            'name' => 'New York 3',
                            'slug' => 'nyc3',
                            'available' => false,
                            'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'prev' => 'https://api.digitalocean.com/v2/regions?page=2&per_page=1',
                            'first' => 'https://api.digitalocean.com/v2/regions?page=1&per_page=1',
                        ],
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getAllRegions();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'regions'));

        $this->assertCount(3, $result);

        /** @var RegionDto $region */
        $region = $result[0];
        $this->assertEquals([
            'name' => 'New York 1',
            'slug' => 'nyc1',
            'available' => true,
            'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
        ], $region->toArray());

        /** @var RegionDto $region */
        $region = $result[1];
        $this->assertEquals([
            'name' => 'New York 2',
            'slug' => 'nyc2',
            'available' => true,
            'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
        ], $region->toArray());

        /** @var RegionDto $region */
        $region = $result[2];
        $this->assertEquals([
            'name' => 'New York 3',
            'slug' => 'nyc3',
            'available' => false,
            'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
        ], $region->toArray());
    }

    public function test_getAllSizes_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/sizes*' => Http::sequence()
                ->push([
                    'sizes' => [
                        [
                            'slug' => 's-1vcpu-1gb',
                            'transfer' => 1,
                            'price_monthly' => 5,
                            'memory' => 1024,
                            'vcpus' => 1,
                            'disk' => 25,
                            'regions' => ['nyc1', 'nyc2'],
                            'available' => true,
                            'description' => 'Basic',
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/sizes?page=2&per_page=1',
                            'last' => 'https://api.digitalocean.com/v2/sizes?page=3&per_page=1',
                        ],
                    ],
                ])
                ->push([
                    'sizes' => [
                        [
                            'slug' => 's-2vcpu-2gb',
                            'transfer' => 1,
                            'price_monthly' => 5,
                            'memory' => 2048,
                            'vcpus' => 2,
                            'disk' => 25,
                            'regions' => ['nyc1', 'nyc2'],
                            'available' => true,
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/sizes?page=3&per_page=1',
                            'last' => 'https://api.digitalocean.com/v2/sizes?page=3&per_page=1',
                            'prev' => 'https://api.digitalocean.com/v2/sizes?page=1&per_page=1',
                            'first' => 'https://api.digitalocean.com/v2/sizes?page=1&per_page=1',
                        ],
                    ],
                ])
                ->push([
                    'sizes' => [
                        [
                            'slug' => 's-3vcpu-3gb',
                            'transfer' => 1,
                            'price_monthly' => 5,
                            'memory' => 3072,
                            'vcpus' => 3,
                            'disk' => 25,
                            'regions' => ['nyc1', 'nyc2'],
                            'available' => false,
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'prev' => 'https://api.digitalocean.com/v2/sizes?page=2&per_page=1',
                            'first' => 'https://api.digitalocean.com/v2/sizes?page=1&per_page=1',
                        ],
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getAllSizes();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'sizes'));

        $this->assertCount(3, $result);

        /** @var SizeDto $size */
        $size = $result[0];
        $this->assertEquals([
            'slug' => 's-1vcpu-1gb',
            'transfer' => 1.0,
            'priceMonthly' => 5.0,
            'memoryMb' => 1024,
            'cpus' => 1,
            'diskGb' => 25,
            'regions' => ['nyc1', 'nyc2'],
            'available' => true,
            'description' => 'Basic',
        ], $size->toArray());

        /** @var SizeDto $size */
        $size = $result[1];
        $this->assertEquals([
            'slug' => 's-2vcpu-2gb',
            'transfer' => 1.0,
            'priceMonthly' => 5.0,
            'memoryMb' => 2048,
            'cpus' => 2,
            'diskGb' => 25,
            'regions' => ['nyc1', 'nyc2'],
            'available' => true,
            'description' => '',
        ], $size->toArray());

        /** @var SizeDto $size */
        $size = $result[2];
        $this->assertEquals([
            'slug' => 's-3vcpu-3gb',
            'transfer' => 1.0,
            'priceMonthly' => 5.0,
            'memoryMb' => 3072,
            'cpus' => 3,
            'diskGb' => 25,
            'regions' => ['nyc1', 'nyc2'],
            'available' => false,
            'description' => '',
        ], $size->toArray());
    }

    public function test_getServer_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/droplets/123' => Http::response([
                'droplet' => [
                    'id' => 123,
                    'name' => 'testserver',
                    'networks' => [
                        'v4' => [
                            ['ip_address' => '10.128.192.124', 'type' => 'public'],
                            ['ip_address' => '192.168.1.2', 'type' => 'private'],
                        ],
                        'v6' => [
                            ['ip_address' => '2604:a880:0:1010::18a:a001', 'type' => 'public'],
                        ],
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getServer('123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'droplets/123'));

        $this->assertEquals([
            'id' => '123',
            'name' => 'testserver',
            'publicIp4' => '10.128.192.124',
        ], $result->toArray());
    }

    public function test_getAllServers_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/droplets*' => Http::sequence()
                ->push([
                    'droplets' => [
                        [
                            'id' => 1,
                            'name' => 'One',
                            'networks' => [
                                'v4' => [
                                    ['ip_address' => '1.1.1.1', 'type' => 'public'],
                                    ['ip_address' => '0.0.0.0', 'type' => 'private'],
                                ],
                                'v6' => [
                                    ['ip_address' => 'foobar', 'type' => 'public'],
                                ],
                            ],
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=2',
                            'last' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=3',
                        ],
                    ],
                ])
                ->push([
                    'droplets' => [
                        [
                            'id' => 2,
                            'name' => 'Two',
                            'networks' => [
                                'v4' => [
                                    ['ip_address' => '2.2.2.2', 'type' => 'public'],
                                    ['ip_address' => '0.0.0.0', 'type' => 'private'],
                                ],
                            ],
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=3',
                            'last' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=3',
                            'prev' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=1',
                            'first' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=1',
                        ],
                    ],
                ])
                ->push([
                    'droplets' => [
                        [
                            'id' => 3,
                            'name' => 'Three',
                            'networks' => [
                                'v4' => [
                                    ['ip_address' => '3.3.3.3', 'type' => 'public'],
                                    ['ip_address' => '0.0.0.0', 'type' => 'private'],
                                ],
                            ],
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'prev' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=2',
                            'first' => 'https://api.digitalocean.com/v2/droplets?per_page=1&page=1',
                        ],
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getAllServers();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'droplets'));

        $this->assertCount(3, $result);

        /** @var ServerDto $server */
        $server = $result[0];
        $this->assertEquals([
            'id' => '1',
            'name' => 'One',
            'publicIp4' => '1.1.1.1',
        ], $server->toArray());

        /** @var ServerDto $server */
        $server = $result[1];
        $this->assertEquals([
            'id' => '2',
            'name' => 'Two',
            'publicIp4' => '2.2.2.2',
        ], $server->toArray());

        /** @var ServerDto $server */
        $server = $result[2];
        $this->assertEquals([
            'id' => '3',
            'name' => 'Three',
            'publicIp4' => '3.3.3.3',
        ], $server->toArray());
    }

    public function test_getServerPublicIp4_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/droplets/123' => Http::response([
                'droplet' => [
                    'id' => 123,
                    'name' => 'testserver',
                    'networks' => [
                        'v4' => [
                            ['ip_address' => '10.128.192.124', 'type' => 'public'],
                            ['ip_address' => '192.168.1.2', 'type' => 'private'],
                        ],
                        'v6' => [
                            ['ip_address' => '2604:a880:0:1010::18a:a001', 'type' => 'public'],
                        ],
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getServerPublicIp4('123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'droplets/123'));

        $this->assertEquals('10.128.192.124', $result);
    }

    public function test_createServer_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/droplets' => Http::response([
                'droplet' => [
                    'id' => 123,
                    'name' => 'testserver',
                    'networks' => [
                        'v4' => [
                            ['ip_address' => '10.128.192.124', 'type' => 'public'],
                            ['ip_address' => '192.168.1.2', 'type' => 'private'],
                        ],
                        'v6' => [
                            ['ip_address' => '2604:a880:0:1010::18a:a001', 'type' => 'public'],
                        ],
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->createServer(new NewServerDto(
            name: 'testserver',
            region: 'nyc1',
            size: 's1',
            type: 'app',
        ), '666');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'POST', 'droplets'));

        $this->assertEquals([
            'id' => '123',
            'name' => 'testserver',
            'publicIp4' => '10.128.192.124',
        ], $result->toArray());
    }

    public function test_getAvailableRegions_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/sizes' => Http::response([
                'sizes' => [
                    [
                        'slug' => 's-1vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 1,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => true,
                        'description' => 'Basic',
                    ],
                    [
                        'slug' => 's-2vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 2,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => true,
                        'description' => 'Basic',
                    ],
                    [
                        'slug' => 's-3vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 3,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => false,
                        'description' => 'Basic',
                    ],
                ],
            ]),
            'https://api.digitalocean.com/v2/regions' => Http::response([
                'regions' => [
                    [
                        'name' => 'New York 1',
                        'slug' => 'nyc1',
                        'available' => true,
                        'sizes' => ['s-1vcpu-1gb', 's-3vcpu-1gb'],
                    ],
                    [
                        'name' => 'New York 2',
                        'slug' => 'nyc2',
                        'available' => true,
                        'sizes' => ['abc1',],
                    ],
                    [
                        'name' => 'New York 3',
                        'slug' => 'nyc3',
                        'available' => false,
                        'sizes' => ['s-1vcpu-1gb', 's-1vcpu-2gb', 's-1vcpu-3gb'],
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getAvailableRegions();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'regions'));
        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'sizes'));

        $this->assertCount(1, $result);

        $this->assertEquals([
            'name' => 'New York 1',
            'slug' => 'nyc1',
            'available' => true,
            'sizes' => ['s-1vcpu-1gb', 's-3vcpu-1gb'],
        ], $result[0]->toArray());
    }

    public function test_getAvailableSizes_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/sizes' => Http::response([
                'sizes' => [
                    [
                        'slug' => 's-1vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 1,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => true,
                        'description' => 'Basic',
                    ],
                    [
                        'slug' => 's-2vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 2,
                        'disk' => 25,
                        'regions' => ['nyc3'],
                        'available' => true,
                        'description' => 'Basic',
                    ],
                    [
                        'slug' => 's-3vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 3,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => false,
                        'description' => 'Basic',
                    ],
                ],
            ]),
            'https://api.digitalocean.com/v2/regions' => Http::response([
                'regions' => [
                    [
                        'name' => 'New York 1',
                        'slug' => 'nyc1',
                        'available' => true,
                        'sizes' => ['s-2vcpu-1gb', 's-3vcpu-1gb'],
                    ],
                    [
                        'name' => 'New York 2',
                        'slug' => 'nyc2',
                        'available' => true,
                        'sizes' => ['abc1',],
                    ],
                    [
                        'name' => 'New York 3',
                        'slug' => 'nyc3',
                        'available' => false,
                        'sizes' => ['s-1vcpu-1gb'],
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getAvailableSizes();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'regions'));
        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'sizes'));

        $this->assertCount(1, $result);

        $this->assertEquals([
            'slug' => 's-1vcpu-1gb',
            'transfer' => 1.0,
            'priceMonthly' => 5.0,
            'memoryMb' => 1024,
            'cpus' => 1,
            'diskGb' => 25,
            'regions' => ['nyc1', 'nyc2'],
            'available' => true,
            'description' => 'Basic',
        ], $result[0]->toArray());
    }

    public function test_getSizesAvailableInRegion_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/sizes' => Http::response([
                'sizes' => [
                    [
                        'slug' => 's-1vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 1,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => true,
                        'description' => 'Basic',
                    ],
                    [
                        'slug' => 's-2vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 2,
                        'disk' => 25,
                        'regions' => ['nyc3'],
                        'available' => true,
                        'description' => 'Basic',
                    ],
                    [
                        'slug' => 's-3vcpu-1gb',
                        'transfer' => 1,
                        'price_monthly' => 5,
                        'memory' => 1024,
                        'vcpus' => 3,
                        'disk' => 25,
                        'regions' => ['nyc1', 'nyc2'],
                        'available' => false,
                        'description' => 'Basic',
                    ],
                ],
            ]),
            'https://api.digitalocean.com/v2/regions' => Http::response([
                'regions' => [
                    [
                        'name' => 'New York 1',
                        'slug' => 'nyc1',
                        'available' => true,
                        'sizes' => ['s-2vcpu-1gb', 's-3vcpu-1gb'],
                    ],
                    [
                        'name' => 'New York 2',
                        'slug' => 'nyc2',
                        'available' => true,
                        'sizes' => ['abc1',],
                    ],
                    [
                        'name' => 'New York 3',
                        'slug' => 'nyc3',
                        'available' => true,
                        'sizes' => ['s-2vcpu-1gb'],
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getSizesAvailableInRegion('nyc1');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'regions'));
        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'sizes'));

        $this->assertCount(1, $result);

        $this->assertEquals([
            'slug' => 's-1vcpu-1gb',
            'transfer' => 1.0,
            'priceMonthly' => 5.0,
            'memoryMb' => 1024,
            'cpus' => 1,
            'diskGb' => 25,
            'regions' => ['nyc1', 'nyc2'],
            'available' => true,
            'description' => 'Basic',
        ], $result[0]->toArray());
    }

    public function test_getSshKey_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys/123' => Http::response([
                'ssh_key' => [
                    "id" => 123,
                    "fingerprint" => "foobar",
                    "public_key" => "example",
                    "name" => "The Key"
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getSshKey('123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account/keys/123'));

        $this->assertEquals([
            "id" => '123',
            "fingerprint" => "foobar",
            "publicKey" => "example",
            "name" => "The Key"
        ], $result->toArray());
    }

    public function test_getAllSshKeys_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys*' => Http::sequence()
                ->push([
                    'ssh_keys' => [
                        [
                            'id' => 1,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example',
                            'name' => 'One',
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=2',
                            'last' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=3',
                        ],
                    ],
                ])
                ->push([
                    'ssh_keys' => [
                        [
                            'id' => 2,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example',
                            'name' => 'Two'
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'next' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=3',
                            'last' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=3',
                            'prev' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=1',
                            'first' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=1',
                        ],
                    ],
                ])
                ->push([
                    'ssh_keys' => [
                        [
                            'id' => 3,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example',
                            'name' => 'Three',
                        ],
                    ],
                    'links' => [
                        'pages' => [
                            'prev' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=2',
                            'first' => 'https://api.digitalocean.com/v2/account/keys?per_page=1&page=1',
                        ],
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getAllSshKeys();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account/keys'));

        $this->assertCount(3, $result);

        /** @var SshKeyDto $key */
        $key = $result[0];
        $this->assertEquals([
            'id' => 1,
            'fingerprint' => 'foobar',
            'publicKey' => 'example',
            'name' => 'One'
        ], $key->toArray());

        /** @var SshKeyDto $key */
        $key = $result[1];
        $this->assertEquals([
            'id' => 2,
            'fingerprint' => 'foobar',
            'publicKey' => 'example',
            'name' => 'Two'
        ], $key->toArray());

        /** @var SshKeyDto $key */
        $key = $result[2];
        $this->assertEquals([
            'id' => 3,
            'fingerprint' => 'foobar',
            'publicKey' => 'example',
            'name' => 'Three'
        ], $key->toArray());
    }

    public function test_addSshKey_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys' => Http::response([
                'ssh_key' => [
                    "id" => 123,
                    "fingerprint" => "finger",
                    "public_key" => "foobar",
                    "name" => "testkey"
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addSshKey('testkey', 'foobar');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'POST', 'account/keys'));

        $this->assertEquals([
            "id" => '123',
            "fingerprint" => "finger",
            "publicKey" => "foobar",
            "name" => "testkey",
        ], $result->toArray());
    }

    public function test_updateSshKey_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys/123' => Http::response([
                'ssh_key' => [
                    "id" => 123,
                    "fingerprint" => "finger",
                    "public_key" => "foobar",
                    "name" => "newname"
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->updateSshKey('123', 'newname');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'PUT', 'account/keys/123'));

        $this->assertEquals([
            "id" => '123',
            "fingerprint" => "finger",
            "publicKey" => "foobar",
            "name" => "newname"
        ], $result->toArray());
    }

    public function test_addSshKeySafely_method_when_key_is_not_yet_added()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys' => Http::sequence()
                ->push([
                    'ssh_keys' => [
                        [
                            'id' => 1,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example1',
                            'name' => 'One',
                        ],
                        [
                            'id' => 2,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example2',
                            'name' => 'Two',
                        ],
                        [
                            'id' => 3,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example3',
                            'name' => 'Three',
                        ],
                    ],
                ])
                ->push([
                    'ssh_key' => [
                        "id" => 123,
                        "fingerprint" => "finger",
                        "public_key" => "pbkey",
                        "name" => "newname"
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addSshKeySafely('newname', 'pbkey');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account/keys'));
        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'POST', 'account/keys'));

        $this->assertEquals([
            "id" => '123',
            "fingerprint" => "finger",
            "publicKey" => "pbkey",
            "name" => "newname"
        ], $result->toArray());
    }

    public function test_addShhKeySafely_method_when_key_is_already_added()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys' => Http::sequence()
                ->push([
                    'ssh_keys' => [
                        [
                            'id' => 1,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example1',
                            'name' => 'One',
                        ],
                        [
                            'id' => 2,
                            'fingerprint' => 'foobar',
                            'public_key' => 'example2',
                            'name' => 'Two',
                        ],
                        [
                            'id' => 123,
                            'fingerprint' => 'finger',
                            'public_key' => 'pbkey',
                            'name' => 'newname',
                        ],
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addSshKeySafely('newname', 'pbkey');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account/keys'));
        Http::assertNotSent(fn(Request $request) => $this->checkRequest($request, 'POST', 'account/keys'));

        $this->assertEquals([
            "id" => '123',
            "fingerprint" => "finger",
            "publicKey" => "pbkey",
            "name" => "newname"
        ], $result->toArray());
    }

    public function test_addSshKeySafely_method_when_key_needs_updating()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/account/keys' => Http::response([
                'ssh_keys' => [
                    [
                        'id' => 1,
                        'fingerprint' => 'foobar',
                        'public_key' => 'example1',
                        'name' => 'One',
                    ],
                    [
                        'id' => 2,
                        'fingerprint' => 'foobar',
                        'public_key' => 'example2',
                        'name' => 'Two',
                    ],
                    [
                        'id' => 123,
                        'fingerprint' => 'foobar',
                        'public_key' => 'pbkey',
                        'name' => 'Three',
                    ],
                ],
            ]),
            'https://api.digitalocean.com/v2/account/keys/123' => Http::response([
                'ssh_key' => [
                    "id" => 123,
                    "fingerprint" => "finger",
                    "public_key" => "pbkey",
                    "name" => "newname",
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addSshKeySafely('newname', 'pbkey');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account/keys'));
        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'PUT', 'account/keys/123'));

        $this->assertEquals([
            "id" => '123',
            "fingerprint" => "finger",
            "publicKey" => "pbkey",
            "name" => "newname",
        ], $result->toArray());
    }

    public function test_refreshToken_method()
    {
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->refreshToken();

        Http::assertNothingSent();

        $this->assertEquals([
            'id' => null,
            'token' => static::TOKEN,
            'refresh_token' => null,
            'expires_at' => null,
        ], $result->toArray());
    }

    protected function api(): DigitalOceanV2
    {
        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2_servers', [
            'token' => new AuthTokenDto(null, static::TOKEN),
        ]);

        return $api;
    }

    protected function checkRequest(Request $request, string $method, string $url): bool
    {
        return $request->method() == $method
            && $request->url() == "https://api.digitalocean.com/v2/{$url}"
            && $request->hasHeader('Authorization', 'Bearer ' . static::TOKEN);
    }
}
