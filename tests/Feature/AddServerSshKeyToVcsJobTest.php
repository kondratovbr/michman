<?php

namespace Tests\Feature;

use App\DataTransferObjects\SshKeyData;
use App\Jobs\Servers\AddServerSshKeyToVcsJob;
use App\Models\ServerSshKey;
use App\Models\User;
use App\Models\VcsProvider;
use App\Services\VcsProviderInterface;
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
                        trim($key->getPublicKey()->toString('OpenSSH', ['comment' => ''])),
                    )
                    ->once()
                    ->andReturn(new SshKeyData(
                        id: '100500',
                        fingerprint: Str::random(),
                        publicKey: trim($key->getPublicKey()->toString('OpenSSH', ['comment' => ''])),
                        name: $server->name . ' - server key',
                    ));
            }
        );

        app()->call([$job, 'handle']);

        $serverSshKey->refresh();

        $this->assertNotNull($serverSshKey);
        $this->assertEquals($server->name . ' - server key', $serverSshKey->name);
        $this->assertEquals(
            trim($key->getPublicKey()->toString('OpenSSH', ['comment' => '']), ' '),
            $serverSshKey->getPublicKeyString(false)
        );
    }
}
