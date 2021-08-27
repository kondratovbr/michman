<?php

namespace Tests\Feature\Servers;

use App\Jobs\Servers\CreateUserOnServerJob;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Models\WorkerSshKey;
use App\Scripts\Root\AddSshKeyToUserScript;
use App\Scripts\Root\CreateGenericUserScript;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;
use Tests\Feature\Traits\MocksSshSessions;

class CreateUserOnServerJobTest extends AbstractFeatureTest
{
    use MocksSshSessions;

    public function test_job_parameters_and_logic()
    {
        /** @var WorkerSshKey $workerSshKey */
        $workerSshKey = WorkerSshKey::factory()->withServer()->create();
        $server = $workerSshKey->server;
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($server->user)->hasAttached($server)->create();

        $job = new CreateUserOnServerJob($server, 'test');

        $this->assertEquals('servers', $job->queue);

        $this->mockSftp();

        $this->mock(CreateGenericUserScript::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    $sshArg,
                ) use ($server) {
                    return $serverArg->is($server)
                        && $usernameArg === 'test'
                        && $sshArg instanceof SFTP;
                })
                ->once();
        });

        $this->mock(AddSshKeyToUserScript::class, function (MockInterface $mock) use ($server, $workerSshKey, $userSshKey) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    WorkerSshKey $keyArg,
                    $sshArg,
                ) use ($server, $workerSshKey) {
                    return $serverArg->is($server)
                        && $usernameArg === 'test'
                        && $keyArg->is($workerSshKey)
                        && $sshArg instanceof SFTP;
                })
                ->once();
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    UserSshKey $keyArg,
                    $sshArg,
                ) use ($server, $userSshKey) {
                    return $serverArg->is($server)
                        && $usernameArg === 'test'
                        && $keyArg->is($userSshKey)
                        && $sshArg instanceof SFTP;
                })
                ->once();
        });

        $this->app->call([$job, 'handle']);
    }
}
