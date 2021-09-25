<?php

namespace Tests\Feature\Daemons;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\RestartDaemonJob;
use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Notifications\Daemons\DaemonFailedNotification;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartDaemonScript;
use App\Scripts\Root\StopDaemonScript;
use App\States\Daemons\Failed;
use App\States\Daemons\Restarting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;
use Tests\Feature\Traits\MocksSshSessions;

class RestartDaemonJobTest extends AbstractFeatureTest
{
    use MocksSshSessions;

    public function test_eligible_daemon_gets_restarted()
    {
        /** @var Server $server */
        $server = WorkerSshKey::factory()
            ->withServer()
            ->create()->server;

        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->for($server)
            ->inState('restarting')
            ->create();

        $job = new RestartDaemonJob($daemon);

        $this->mockSftp();

        $this->mock(StopDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
                    SFTP $sshArg,
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once();
        });

        $this->mock(StartDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
                    SFTP $sshArg,
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once();
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Bus::assertDispatched(UpdateDaemonStateJob::class);
        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonUpdatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('daemons', [
            'id' => $daemon->id,
            'state' => 'restarting',
        ]);

        $daemon->refresh();

        $this->assertTrue($daemon->exists);
        $this->assertTrue($daemon->state->is(Restarting::class));
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_daemons_get_ignored(string $state)
    {
        /** @var Server $server */
        $server = WorkerSshKey::factory()
            ->withServer()
            ->create()->server;

        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->for($server)
            ->inState($state)
            ->create();

        $job = new RestartDaemonJob($daemon);

        $this->mockSftp();

        $this->mock(StopDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldNotHaveBeenCalled();
        });

        $this->mock(StartDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldNotHaveBeenCalled();
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Bus::assertNotDispatched(UpdateDaemonStateJob::class);
        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonUpdatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('daemons', [
            'id' => $daemon->id,
            'state' => $state,
        ]);

        $daemon->refresh();

        $this->assertTrue($daemon->exists);
        $this->assertEquals($state, $daemon->state->getValue());
    }

    public function ineligibleStates(): array
    {
        return [
            ['active'],
            ['deleting'],
            ['failed'],
            ['starting'],
            ['stopped'],
            ['stopping'],
        ];
    }

    public function test_failure_gets_handled()
    {
        /** @var Server $server */
        $server = WorkerSshKey::factory()
            ->withServer()
            ->create()->server;

        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->for($server)
            ->inState('restarting')
            ->create();

        $job = new RestartDaemonJob($daemon);

        $this->mockSftp();

        $this->mock(StopDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
                    SFTP $sshArg,
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once();
        });

        $this->mock(StartDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
                    SFTP $sshArg,
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andThrowExceptions([new ServerScriptException]);
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Bus::assertNotDispatched(UpdateDaemonStateJob::class);
        Event::assertDispatched(DaemonUpdatedEvent::class);
        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
        Notification::assertSentTo($daemon->user, DaemonFailedNotification::class);

        $this->assertDatabaseHas('daemons', [
            'id' => $daemon->id,
            'state' => 'failed',
        ]);

        $daemon->refresh();

        $this->assertTrue($daemon->exists);
        $this->assertTrue($daemon->state->is(Failed::class));
    }
}
