<?php

namespace Tests\Feature;

use App\DataTransferObjects\SshKeyDto;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
use App\Models\ServerSshKey;
use App\Models\User;
use App\Models\VcsProvider;
use App\Services\VcsProviderInterface;
use App\Support\SshKeyFormatter;
use App\Support\Str;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class AddServerSshKeyToVcsJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory()->for($user)->create([
            'provider' => 'github_v3',
        ]);
        /** @var ServerSshKey $serverSshKey */
        $serverSshKey = ServerSshKey::factory()->withServer()->create();
        $key = $serverSshKey->privateKey;
        $server = $serverSshKey->server;

        $job = new AddServerSshKeyToVcsJob($server, $vcsProvider);

        $this->assertEquals('providers', $job->queue);

        $this->mockBind('github_v3', VcsProviderInterface::class,
            function (MockInterface $mock) use ($server, $key) {
                $mock->shouldReceive('addSshKeySafely')
                    ->with(
                        $server->name . ' - server key',
                        SshKeyFormatter::format($key->getPublicKey()),
                    )
                    ->once()
                    ->andReturn(new SshKeyDto(
                        id: '100500',
                        fingerprint: Str::random(),
                        publicKey: SshKeyFormatter::format($key->getPublicKey()),
                        name: $server->name . ' - server key',
                    ));
            }
        );

        app()->call([$job, 'handle']);

        $serverSshKey->refresh();

        $this->assertNotNull($serverSshKey);
        $this->assertEquals(
            $server->name . ' - server key',
            $serverSshKey->name
        );
        $this->assertEquals(
            SshKeyFormatter::format($key->getPublicKey()),
            $serverSshKey->getPublicKeyString(false)
        );
    }
}
