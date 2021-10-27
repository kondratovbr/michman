<?php

namespace Tests\Feature\VcsProviders;

use App\DataTransferObjects\SshKeyDto;
use App\Models\VcsProvider;
use App\Services\VcsProviderInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\AbstractFeatureTest;

class GitHubV3Test extends AbstractFeatureTest
{
    public const TOKEN = '666666';

    public function test_credentials_are_valid_method()
    {
        $api = $this->api();

        Http::fake();

        $result = $api->credentialsAreValid();

        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Accept', 'application/vnd.github.v3+json')
                && $request->method() == 'GET'
                && $request->url() == 'https://api.github.com/user'
                && $request->hasHeader('Authorization', 'Bearer ' . static::TOKEN);
        });

        $this->assertTrue($result);
    }

    public function test_get_ssh_key_method()
    {
        $api = $this->api();

        Http::fake([
            'https://api.github.com/user/keys/123' => Http::response([
                "key" => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
                "id" => 123,
                "url" => "https://api.github.com/user/keys/123",
                "title" => "ssh-rsa AAAAB3NzaC1yc2EAAA",
                "created_at" => "2020-06-11T21:31:57Z",
                "verified" => false,
                "read_only" => false
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $api->getSshKey('123');

        Http::assertSent(
            fn(Request $request) => $this->checkRequest($request, 'GET', 'user/keys/123')
        );

        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            'name' => "ssh-rsa AAAAB3NzaC1yc2EAAA",
            'id' => '123',
            'fingerprint' => null,
        ], $result->toArray());
    }

    public function test_get_all_ssh_keys_method_paginated()
    {
        $api = $this->api();

        Http::fake([
            'https://api.github.com/*' => Http::sequence()
                ->push([[
                    "key" => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
                    "id" => 12,
                    "url" => "https://api.github.com/user/keys/12",
                    "title" => "ssh-rsa AAAAB3NzaC1yc2EAAA",
                    "created_at" => "2020-06-11T21:31:57Z",
                    "verified" => false,
                    "read_only" => false
                ]], 200, ['Link' => '<https://api.github.com/user/keys?per_page=1&page=2>; rel="next", <https://api.github.com/user/keys?per_page=1&page=3>; rel="last"'])
                ->push([[
                    "key" => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv4321",
                    "id" => 13,
                    "url" => "https://api.github.com/user/keys/13",
                    "title" => "ssh-rsa AAAAB3NzaC1yc2EBBB",
                    "created_at" => "2020-06-11T21:31:57Z",
                    "verified" => false,
                    "read_only" => false
                ]], 200, ['Link' => '<https://api.github.com/user/keys?per_page=1&page=3>; rel="next", <https://api.github.com/user/keys?per_page=1&page=3>; rel="last", <https://api.github.com/user/keys?per_page=1&page=1>; rel="first", <https://api.github.com/user/keys?per_page=1&page=1>; rel="prev"'])
                ->push([[
                    "key" => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv6666",
                    "id" => 21,
                    "url" => "https://api.github.com/user/keys/21",
                    "title" => "ssh-rsa AAAAB3NzaC1yc2ECCC",
                    "created_at" => "2020-06-11T21:31:57Z",
                    "verified" => false,
                    "read_only" => false
                ]], 200, ['Link' => '<https://api.github.com/user/keys?per_page=1&page=1>; rel="first", <https://api.github.com/user/keys?per_page=1&page=2>; rel="prev"']),
            '*' => Http::response(null, 500),
        ]);

        $result = $api->getAllSshKeys();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'user/keys'));

        $this->assertCount(3, $result);

        /** @var SshKeyDto $key */
        $key = $result[0];
        $this->assertTrue($key instanceof SshKeyDto);
        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            'name' => "ssh-rsa AAAAB3NzaC1yc2EAAA",
            'id' => '12',
            'fingerprint' => null,
        ], $key->toArray());

        /** @var SshKeyDto $key */
        $key = $result[1];
        $this->assertTrue($key instanceof SshKeyDto);
        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv4321",
            'name' => "ssh-rsa AAAAB3NzaC1yc2EBBB",
            'id' => '13',
            'fingerprint' => null,
        ], $key->toArray());

        /** @var SshKeyDto $key */
        $key = $result[2];
        $this->assertTrue($key instanceof SshKeyDto);
        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv6666",
            'name' => "ssh-rsa AAAAB3NzaC1yc2ECCC",
            'id' => '21',
            'fingerprint' => null,
        ], $key->toArray());
    }

    public function test_add_ssh_key_method()
    {
        $api = $this->api();

        Http::fake([
            'https://api.github.com/user/keys' => Http::response([
                "key" => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
                "id" => 123,
                "url" => "https://api.github.com/user/keys/123",
                "title" => "Key name",
                "created_at" => "2020-06-11T21:31:57Z",
                "verified" => false,
                "read_only" => false
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $api->addSshKey('Key name', '2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'POST', 'user/keys'));

        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            'name' => "Key name",
            'id' => '123',
            'fingerprint' => null,
        ], $result->toArray());
    }

    public function test_delete_ssh_key_method()
    {
        $api = $this->api();

        Http::fake([
            'https://api.github.com/user/keys/123' => Http::response(null, 204),
            '*' => Http::response(null, 500),
        ]);

        $api->deleteSshKey('123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'user/keys/123'));
    }

    public function test_update_ssh_key_method()
    {
        $api = $this->api();

        Http::fake([
            'https://api.github.com/user/keys/123' => Http::response(null, 204),
            'https://api.github.com/user/keys' => Http::response([
                "key" => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
                "id" => 611,
                "url" => "https://api.github.com/user/keys/611",
                "title" => "Key name",
                "created_at" => "2020-06-11T21:31:57Z",
                "verified" => false,
                "read_only" => false
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $api->updateSshKey(new SshKeyDto(
            publicKey: "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            name: 'Key name',
            id: '123',
        ));

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'user/keys/123'));

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'POST', 'user/keys'));

        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            'name' => "Key name",
            'id' => '611',
            'fingerprint' => null,
        ], $result->toArray());
    }

    protected function checkRequest(Request $request, string $method, string $url): bool
    {
        return $request->hasHeader('Accept', 'application/vnd.github.v3+json')
            && $request->method() == $method
            && $request->url() == "https://api.github.com/{$url}"
            && $request->hasHeader('Authorization', 'Bearer ' . static::TOKEN);
    }

    protected function api(): VcsProviderInterface
    {
        /** @var VcsProvider $vcs */
        $vcs = VcsProvider::factory([
            'provider' => 'github_v3',
        ])->withUser()->create();

        $vcs->token = static::TOKEN;
        $vcs->save();

        return $vcs->api();
    }
}
