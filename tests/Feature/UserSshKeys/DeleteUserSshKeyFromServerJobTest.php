<?php

namespace Tests\Feature\UserSshKeys;

use App\Jobs\UserSshKeys\DeleteUserSshKeyFromServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Models\WorkerSshKey;
use App\Scripts\Root\DeleteSshKeyFromUserScript;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;
use Tests\Feature\Traits\MocksSshSessions;

class DeleteUserSshKeyFromServerJobTest extends AbstractFeatureTest
{
    use MocksSshSessions;

    public function test_job_parameters_and_logic()
    {
        /** @var WorkerSshKey $workerSshKey */
        $workerSshKey = WorkerSshKey::factory()->withServer()->create();
        $server = $workerSshKey->server;
        $user = $server->user;
        /** @var UserSshKey $userSshKey */
        $userSshKey = UserSshKey::factory()->for($user)->hasAttached($server)->create();
        /** @var Collection $projects */
        $projects = Project::factory()->for($user)->hasAttached($server)->count(2)->create();

        $this->mockSftp();

        $this->mockBind(DeleteSshKeyFromUserScript::class, function (MockInterface $mock) use ($server, $userSshKey, $projects) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    UserSshKey $keyArg,
                    $ssh,
                ) use ($server, $userSshKey) {
                    return $serverArg->is($server)
                        && $usernameArg === 'michman'
                        && $keyArg->is($userSshKey)
                        && $ssh instanceof SFTP;
                })
                ->once();

            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    UserSshKey $keyArg,
                    $ssh,
                ) use ($server, $userSshKey, $projects) {
                    return $serverArg->is($server)
                        && $usernameArg === $projects[0]->serverUsername
                        && $keyArg->is($userSshKey)
                        && $ssh instanceof SFTP;
                })
                ->once();

            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    UserSshKey $keyArg,
                    $ssh,
                ) use ($server, $userSshKey, $projects) {
                    return $serverArg->is($server)
                        && $usernameArg === $projects[1]->serverUsername
                        && $keyArg->is($userSshKey)
                        && $ssh instanceof SFTP;
                })
                ->once();
        });

        $job = new DeleteUserSshKeyFromServerJob($userSshKey, $server);

        $this->app->call([$job, 'handle']);
    }
}
