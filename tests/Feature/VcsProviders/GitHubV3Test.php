<?php

namespace Tests\Feature\VcsProviders;

use App\DataTransferObjects\SshKeyDto;
use App\DataTransferObjects\WebhookDto;
use App\Models\VcsProvider;
use App\Services\GitHubV3;
use App\Services\VcsProviderInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\AbstractFeatureTest;

class GitHubV3Test extends AbstractFeatureTest
{
    public const TOKEN = '666666';

    public function test_credentialsAreValid_method()
    {
        Http::fake();

        $result = $this->api()->credentialsAreValid();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'https://api.github.com/user'));

        $this->assertTrue($result);
    }

    public function test_credentialsAreValid_method_when_invalid()
    {
        Http::fake([
            '*' => Http::response(null, 403),
        ]);

        $result = $this->api()->credentialsAreValid();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'https://api.github.com/user'));

        $this->assertFalse($result);
    }

    public function test_getSshKey_method()
    {
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

        $result = $this->api()->getSshKey('123');

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

    public function test_getAllSshKeys_method_paginated()
    {
        Http::fake([
            'https://api.github.com/user/keys*' => Http::sequence()
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

        $result = $this->api()->getAllSshKeys();

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

    public function test_addSshKey_method()
    {
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

        $result = $this->api()->addSshKey('Key name', '2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234');

        Http::assertSent(function (Request $request) {
            return $this->checkRequest($request, 'POST', 'user/keys')
                && $request->data() == [
                    'title' => 'Key name',
                    'key' => '2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234',
                ];
        });

        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            'name' => "Key name",
            'id' => '123',
            'fingerprint' => null,
        ], $result->toArray());
    }

    public function test_deleteSshKey_method()
    {
        Http::fake([
            'https://api.github.com/user/keys/123' => Http::response(null, 204),
            '*' => Http::response(null, 500),
        ]);

        $this->api()->deleteSshKey('123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'user/keys/123'));
    }

    public function test_updateSshKey_method()
    {
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

        $result = $this->api()->updateSshKey(new SshKeyDto(
            publicKey: "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            name: 'Key name',
            id: '123',
        ));

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'user/keys/123'));

        Http::assertSent(function (Request $request) {
            return $this->checkRequest($request, 'POST', 'user/keys')
                && $request->data() == [
                    'title' => 'Key name',
                    'key' => '2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234',
                ];
        });

        $this->assertEquals([
            'publicKey' => "2Sg8iYjAxxmI2LvUXpJjkYrMxURPc8r+dB7TJyvv1234",
            'name' => "Key name",
            'id' => '611',
            'fingerprint' => null,
        ], $result->toArray());
    }

    public function test_getLatestCommitHash_method_with_full_repo_name()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/commits/master' => Http::response([
                "url" => "https://api.github.com/repos/user/repo/commits/6dcb09b5b57875f334f61aebed695e2e4193db5e",
                "sha" => "6dcb09b5b57875f334f61aebed695e2e4193db5e",
                "node_id" => "MDY6Q29tbWl0NmRjYjA5YjViNTc4NzVmMzM0ZjYxYWViZWQ2OTVlMmU0MTkzZGI1ZQ==",
                "html_url" => "https://github.com/user/repo/commit/6dcb09b5b57875f334f61aebed695e2e4193db5e",
                "comments_url" => "https://api.github.com/repos/user/repo/commits/6dcb09b5b57875f334f61aebed695e2e4193db5e/comments",
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getLatestCommitHash('user/repo', 'master');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/commits/master'));

        $this->assertEquals('6dcb09b5b57875f334f61aebed695e2e4193db5e', $result);
    }

    public function test_getLatestCommitHash_method_with_username_and_repo_name()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/commits/master' => Http::response([
                "url" => "https://api.github.com/repos/user/repo/commits/6dcb09b5b57875f334f61aebed695e2e4193db5e",
                "sha" => "6dcb09b5b57875f334f61aebed695e2e4193db5e",
                "node_id" => "MDY6Q29tbWl0NmRjYjA5YjViNTc4NzVmMzM0ZjYxYWViZWQ2OTVlMmU0MTkzZGI1ZQ==",
                "html_url" => "https://github.com/user/repo/commit/6dcb09b5b57875f334f61aebed695e2e4193db5e",
                "comments_url" => "https://api.github.com/repos/user/repo/commits/6dcb09b5b57875f334f61aebed695e2e4193db5e/comments",
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getLatestCommitHash(null, 'master', 'user', 'repo');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/commits/master'));

        $this->assertEquals('6dcb09b5b57875f334f61aebed695e2e4193db5e', $result);
    }

    public function test_getSshHostKey_method()
    {
        $result = $this->api()->getSshHostKey();

        $this->assertNotEmpty($result);
    }

    public function test_getFullSshString_method()
    {
        $result = GitHubV3::getFullSshString('user/repo');

        $this->assertEquals('git@github.com:user/repo.git', $result);
    }

    public function test_getWebhook_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks/123' => Http::response([
                "type" => "Repository",
                "id" => 123,
                "name" => "web",
                "active" => true,
                "events" => [
                    "push",
                ],
                "config" => [
                    "content_type" => "json",
                    "insecure_ssl" => "0",
                    "url" => "https://example.com/webhook",
                ],
                "updated_at" => "2019-06-03T00:57:16Z",
                "created_at" => "2019-06-03T00:57:16Z",
                "url" => "https://api.github.com/repos/user/repo/hooks/123",
                "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                "last_response" => [
                    "code" => null,
                    "status" => "unused",
                    "message" => null,
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getWebhook('user/repo', '123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks/123'));

        $this->assertEquals([
            'events' => ['push'],
            'id' => '123',
            'url' => 'https://example.com/webhook',
        ], $result->toArray());
    }

    public function test_getRepoWebhooks_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks*' => Http::sequence()
                ->push([[
                    "type" => "Repository",
                    "id" => 12,
                    "name" => "web",
                    "active" => true,
                    "events" => ["push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                ]], 200, ['Link' => '<https://api.github.com/repos/user/repo/hooks?per_page=1&page=2>; rel="next", <https://api.github.com/repos/user/repo/hooks?per_page=1&page=3>; rel="last"'])
                ->push([[
                    "type" => "Repository",
                    "id" => 13,
                    "name" => "web",
                    "active" => true,
                    "events" => ["push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                ]], 200, ['Link' => '<https://api.github.com/repos/user/repo/hooks?per_page=1&page=3>; rel="next", <https://api.github.com/repos/user/repo/hooks?per_page=1&page=3>; rel="last", <https://api.github.com/repos/user/repo/hooks?per_page=1&page=1>; rel="first", <https://api.github.com/repos/user/repo/hooks?per_page=1&page=1>; rel="prev"'])
                ->push([[
                    "type" => "Repository",
                    "id" => 66,
                    "name" => "web",
                    "active" => true,
                    "events" => ["push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                ]], 200, ['Link' => '<https://api.github.com/repos/user/repo/hooks?per_page=1&page=1>; rel="first", <https://api.github.com/repos/user/repo/hooks?per_page=1&page=2>; rel="prev"']),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getRepoWebhooks('user/repo');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        $this->assertCount(3, $result);

        /** @var WebhookDto $hook */
        $hook = $result[0];
        $this->assertEquals([
            'events' => ['push'],
            'id' => '12',
            'url' => 'https://example.com/webhook',
        ], $hook->toArray());

        /** @var WebhookDto $hook */
        $hook = $result[1];
        $this->assertEquals([
            'events' => ['push'],
            'id' => '13',
            'url' => 'https://example.com/webhook',
        ], $hook->toArray());

        /** @var WebhookDto $hook */
        $hook = $result[2];
        $this->assertEquals([
            'events' => ['push'],
            'id' => '66',
            'url' => 'https://example.com/webhook',
        ], $hook->toArray());
    }

    public function test_addWebhookPush_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::response([
                "type" => "Repository",
                "id" => 123,
                "name" => "web",
                "active" => true,
                "events" => [
                    "push",
                ],
                "config" => [
                    "content_type" => "json",
                    "insecure_ssl" => "0",
                    "url" => "https://example.com/webhook",
                ],
                "updated_at" => "2019-06-03T00:57:16Z",
                "created_at" => "2019-06-03T00:57:16Z",
                "url" => "https://api.github.com/repos/user/repo/hooks/123",
                "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                "last_response" => [
                    "code" => null,
                    "status" => "unused",
                    "message" => null,
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addWebhookPush('user/repo', 'https://example.com/webhook', '1234567');

        Http::assertSent(function (Request $request) {
            return $this->checkRequest($request, 'POST', 'repos/user/repo/hooks')
                && $request->data() == ['config' => [
                    'url' => 'https://example.com/webhook',
                    'content_type' => 'json',
                    'insecure_ssl' => false,
                    'secret' => '1234567',
                    'events' => ['push'],
                ]];
        });

        $this->assertEquals([
            'events' => ['push'],
            'id' => '123',
            'url' => 'https://example.com/webhook',
        ], $result->toArray());
    }

    public function test_getWebhookIfExistsPush_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::response([
                [
                    "type" => "Repository",
                    "id" => 12,
                    "name" => "web",
                    "active" => true,
                    "events" => ["pull_request"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 123,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping", "push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 66,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook_dump",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getWebhookIfExistsPush('user/repo', 'https://example.com/webhook');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        $this->assertEquals([
            'events' => ['ping', 'push'],
            'id' => '123',
            'url' => 'https://example.com/webhook',
        ], $result->toArray());
    }

    public function test_getWebhookIfExistsPush_method_when_hook_doesnt_exist()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::response([
                [
                    "type" => "Repository",
                    "id" => 12,
                    "name" => "web",
                    "active" => true,
                    "events" => ["pull_request"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 123,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping", "push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/random_webhooks",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 66,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook_dump",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getWebhookIfExistsPush('user/repo', 'https://example.com/webhook');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        $this->assertNull($result);
    }

    public function test_updateWebhookPush_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks/123' => Http::response([
                "type" => "Repository",
                "id" => 123,
                "name" => "web",
                "active" => true,
                "events" => [
                    "push",
                    "ping",
                ],
                "config" => [
                    "content_type" => "json",
                    "insecure_ssl" => "0",
                    "url" => "https://example.com/webhook",
                ],
                "updated_at" => "2019-06-03T00:57:16Z",
                "created_at" => "2019-06-03T00:57:16Z",
                "url" => "https://api.github.com/repos/user/repo/hooks/123",
                "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                "last_response" => [
                    "code" => null,
                    "status" => "unused",
                    "message" => null,
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->updateWebhookPush(
            'user/repo',
            '123',
            'https://example.com/webhook',
            '1234567',
        );

        Http::assertSent(function (Request $request) {
            return $this->checkRequest($request, 'PATCH', 'repos/user/repo/hooks/123')
                && $request->data() == ['config' => [
                    'url' => 'https://example.com/webhook',
                    'content_type' => 'json',
                    'insecure_ssl' => false,
                    'secret' => '1234567',
                    'events' => ['push'],
                ]];
        });

        $this->assertEquals([
            'events' => ['push', 'ping'],
            'id' => '123',
            'url' => 'https://example.com/webhook',
        ], $result->toArray());
    }

    public function test_addWebhookSafelyPush_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::sequence()
                ->push([
                    [
                        "type" => "Repository",
                        "id" => 12,
                        "name" => "web",
                        "active" => true,
                        "events" => ["pull_request"],
                        "config" => [
                            "content_type" => "json",
                            "insecure_ssl" => "0",
                            "url" => "https://example.com/webhook",
                        ],
                        "updated_at" => "2019-06-03T00:57:16Z",
                        "created_at" => "2019-06-03T00:57:16Z",
                        "url" => "https://api.github.com/repos/user/repo/hooks/123",
                        "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                        "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                        "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                        "last_response" => [
                            "code" => null,
                            "status" => "unused",
                            "message" => null,
                        ],
                    ],
                    [
                        "type" => "Repository",
                        "id" => 123,
                        "name" => "web",
                        "active" => true,
                        "events" => ["ping", "push"],
                        "config" => [
                            "content_type" => "json",
                            "insecure_ssl" => "0",
                            "url" => "https://example.com/webhooks_dump",
                        ],
                        "updated_at" => "2019-06-03T00:57:16Z",
                        "created_at" => "2019-06-03T00:57:16Z",
                        "url" => "https://api.github.com/repos/user/repo/hooks/123",
                        "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                        "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                        "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                        "last_response" => [
                            "code" => null,
                            "status" => "unused",
                            "message" => null,
                        ],
                    ],
                    [
                        "type" => "Repository",
                        "id" => 66,
                        "name" => "web",
                        "active" => true,
                        "events" => ["ping"],
                        "config" => [
                            "content_type" => "json",
                            "insecure_ssl" => "0",
                            "url" => "https://example.com/webhook_dump",
                        ],
                        "updated_at" => "2019-06-03T00:57:16Z",
                        "created_at" => "2019-06-03T00:57:16Z",
                        "url" => "https://api.github.com/repos/user/repo/hooks/123",
                        "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                        "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                        "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                        "last_response" => [
                            "code" => null,
                            "status" => "unused",
                            "message" => null,
                        ],
                    ],
                ])
                ->push([
                    "type" => "Repository",
                    "id" => 666,
                    "name" => "web",
                    "active" => true,
                    "events" => ["push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addWebhookSafelyPush('user/repo', 'https://example.com/webhook', '1234567');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        Http::assertSent(function (Request $request) {
            return $this->checkRequest($request, 'POST', 'repos/user/repo/hooks')
                && $request->data() == ['config' => [
                    'url' => 'https://example.com/webhook',
                    'content_type' => 'json',
                    'insecure_ssl' => false,
                    'secret' => '1234567',
                    'events' => ['push'],
                ]];
        });

        $this->assertEquals([
            'events' => ['push'],
            'id' => '666',
            'url' => 'https://example.com/webhook',
        ], $result->toArray());
    }

    public function test_addWebhookSafelyPush_method_when_webhook_exists()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::sequence()
                ->push([
                    [
                        "type" => "Repository",
                        "id" => 12,
                        "name" => "web",
                        "active" => true,
                        "events" => ["pull_request"],
                        "config" => [
                            "content_type" => "json",
                            "insecure_ssl" => "0",
                            "url" => "https://example.com/webhook",
                        ],
                        "updated_at" => "2019-06-03T00:57:16Z",
                        "created_at" => "2019-06-03T00:57:16Z",
                        "url" => "https://api.github.com/repos/user/repo/hooks/123",
                        "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                        "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                        "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                        "last_response" => [
                            "code" => null,
                            "status" => "unused",
                            "message" => null,
                        ],
                    ],
                    [
                        "type" => "Repository",
                        "id" => 123,
                        "name" => "web",
                        "active" => true,
                        "events" => ["ping", "push"],
                        "config" => [
                            "content_type" => "json",
                            "insecure_ssl" => "0",
                            "url" => "https://example.com/webhook",
                        ],
                        "updated_at" => "2019-06-03T00:57:16Z",
                        "created_at" => "2019-06-03T00:57:16Z",
                        "url" => "https://api.github.com/repos/user/repo/hooks/123",
                        "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                        "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                        "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                        "last_response" => [
                            "code" => null,
                            "status" => "unused",
                            "message" => null,
                        ],
                    ],
                    [
                        "type" => "Repository",
                        "id" => 66,
                        "name" => "web",
                        "active" => true,
                        "events" => ["ping"],
                        "config" => [
                            "content_type" => "json",
                            "insecure_ssl" => "0",
                            "url" => "https://example.com/webhook_dump",
                        ],
                        "updated_at" => "2019-06-03T00:57:16Z",
                        "created_at" => "2019-06-03T00:57:16Z",
                        "url" => "https://api.github.com/repos/user/repo/hooks/123",
                        "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                        "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                        "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                        "last_response" => [
                            "code" => null,
                            "status" => "unused",
                            "message" => null,
                        ],
                    ],
                ]),
            'https://api.github.com/repos/user/repo/hooks/123' => Http::response([
                "type" => "Repository",
                "id" => 123,
                "name" => "web",
                "active" => true,
                "events" => ["push"],
                "config" => [
                    "content_type" => "json",
                    "insecure_ssl" => "0",
                    "url" => "https://example.com/webhook",
                ],
                "updated_at" => "2019-06-03T00:57:16Z",
                "created_at" => "2019-06-03T00:57:16Z",
                "url" => "https://api.github.com/repos/user/repo/hooks/123",
                "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                "last_response" => [
                    "code" => null,
                    "status" => "unused",
                    "message" => null,
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->addWebhookSafelyPush('user/repo', 'https://example.com/webhook', '1234567');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        Http::assertSent(function (Request $request) {
            return $this->checkRequest($request, 'PATCH', 'repos/user/repo/hooks/123')
                && $request->data() == ['config' => [
                    'url' => 'https://example.com/webhook',
                    'content_type' => 'json',
                    'insecure_ssl' => false,
                    'secret' => '1234567',
                    'events' => ['push'],
                ]];
        });

        $this->assertEquals([
            'events' => ['push'],
            'id' => '123',
            'url' => 'https://example.com/webhook',
        ], $result->toArray());
    }

    public function test_deleteWebhook_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks/123' => Http::response(null, 204),
            '*' => Http::response(null, 500),
        ]);

        $this->api()->deleteWebhook('user/repo', '123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'repos/user/repo/hooks/123'));
    }

    public function test_deleteWebhookIfExists_method()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::response([
                [
                    "type" => "Repository",
                    "id" => 12,
                    "name" => "web",
                    "active" => true,
                    "events" => ["pull_request"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 123,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping", "push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 66,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook_dump",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
            ]),
            'https://api.github.com/repos/user/repo/hooks/123' => Http::response(null, 204),
            '*' => Http::response(null, 500),
        ]);

        $this->api()->deleteWebhookIfExistsPush('user/repo', 'https://example.com/webhook');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'repos/user/repo/hooks/123'));
    }

    public function test_deleteWebhookIfExists_method_when_hook_doesnt_exist()
    {
        Http::fake([
            'https://api.github.com/repos/user/repo/hooks' => Http::response([
                [
                    "type" => "Repository",
                    "id" => 12,
                    "name" => "web",
                    "active" => true,
                    "events" => ["pull_request"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 123,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping", "push"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhooks_dump",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
                [
                    "type" => "Repository",
                    "id" => 66,
                    "name" => "web",
                    "active" => true,
                    "events" => ["ping"],
                    "config" => [
                        "content_type" => "json",
                        "insecure_ssl" => "0",
                        "url" => "https://example.com/webhook_dump",
                    ],
                    "updated_at" => "2019-06-03T00:57:16Z",
                    "created_at" => "2019-06-03T00:57:16Z",
                    "url" => "https://api.github.com/repos/user/repo/hooks/123",
                    "test_url" => "https://api.github.com/repos/user/repo/hooks/123/test",
                    "ping_url" => "https://api.github.com/repos/user/repo/hooks/123/pings",
                    "deliveries_url" => "https://api.github.com/repos/user/repo/hooks/123/deliveries",
                    "last_response" => [
                        "code" => null,
                        "status" => "unused",
                        "message" => null,
                    ],
                ],
            ]),
            '*' => Http::response(null, 500),
        ]);

        $this->api()->deleteWebhookIfExistsPush('user/repo', 'https://example.com/webhook');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'repos/user/repo/hooks'));

        Http::assertNotSent(fn(Request $request) => $this->checkRequest($request, 'DELETE', 'repos/user/repo/hooks/123'));
    }

    public function test_refreshToken_method()
    {
        $result = $this->api()->refreshToken('1234567890');

        $this->assertEquals([
            'token' => static::TOKEN,
            'refresh_token' => null,
            'expires_at' => null,
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
