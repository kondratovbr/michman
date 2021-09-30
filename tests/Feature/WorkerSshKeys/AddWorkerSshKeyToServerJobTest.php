<?php

namespace Tests\Feature\WorkerSshKeys;

use App\Jobs\WorkerSshKeys\AddWorkerSshKeyToServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Notifications\Servers\FailedToAddSshKeyToServerNotification;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\AddSshKeyToUserScript;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;
use Tests\Feature\Traits\MocksSshSessions;

class AddWorkerSshKeyToServerJobTest extends AbstractFeatureTest
{
    use MocksSshSessions;

    public function test_key_gets_added()
    {
        /** @var WorkerSshKey $key */
        $key = WorkerSshKey::factory()->withServer()->create();
        $server = $key->server;

        /** @var Collection $projects */
        $projects = Project::factory()
            ->for($server->user)
            ->hasAttached($server)
            ->count(3)
            ->create();

        $job = new AddWorkerSshKeyToServerJob($server);

        $this->mockSftp();

        $this->mock(AddSshKeyToUserScript::class, function (MockInterface $mock) use ($server, $key) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    WorkerSshKey $keyArg,
                    SFTP $sshArg,
                ) use ($server, $key) {
                    return $serverArg->is($server)
                        && $keyArg->is($key);
                })
                ->times(4);
        });

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('worker_ssh_keys', [
            'id' => $key->id,
        ]);
    }

    public function test_failure_gets_handled()
    {
        /** @var WorkerSshKey $key */
        $key = WorkerSshKey::factory()->withServer()->create();
        $server = $key->server;

        /** @var Collection $projects */
        $projects = Project::factory()
            ->for($server->user)
            ->hasAttached($server)
            ->count(3);

        $job = new AddWorkerSshKeyToServerJob($server);

        $this->mockSftp();

        $this->mock(AddSshKeyToUserScript::class, function (MockInterface $mock) use ($server, $key) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $usernameArg,
                    WorkerSshKey $keyArg,
                    SFTP $sshArg,
                ) use ($server, $key) {
                    return $serverArg->is($server)
                        && $keyArg->is($key);
                })
                ->once()
                ->andThrow(new ServerScriptException);
        });

        Notification::fake();

        $caught = false;
        try {
            $this->app->call([$job, 'handle']);
        } catch (ServerScriptException) {
            $caught = true;
        }

        $this->assertTrue($caught);

        $this->app->call([$job, 'failed']);

        $this->assertDatabaseHas('worker_ssh_keys', [
            'id' => $key->id,
        ]);

        Notification::assertSentTo($key->user, FailedToAddSshKeyToServerNotification::class);
    }
}
