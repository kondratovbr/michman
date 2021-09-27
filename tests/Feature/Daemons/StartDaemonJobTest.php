<?php

namespace Tests\Feature\Daemons;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\StartDaemonJob;
use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use App\Notifications\Daemons\DaemonFailedNotification;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartDaemonScript;
use App\States\Daemons\Failed;
use App\States\Daemons\Starting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class StartDaemonJobTest extends AbstractFeatureTest
{
    public function test_eligible_daemon_gets_started()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('starting')
            ->create();

        $job = new StartDaemonJob($daemon);

        $this->mock(StartDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
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
            'state' => 'starting',
        ]);

        $daemon->refresh();

        $this->assertTrue($daemon->exists);
        $this->assertTrue($daemon->state->is(Starting::class));
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_daemons_get_ignored(string $state)
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        $job = new StartDaemonJob($daemon);

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
            ['restarting'],
            ['stopped'],
            ['stopping'],
        ];
    }

    public function test_failure_gets_handled()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('starting')
            ->create();

        $job = new StartDaemonJob($daemon);

        $this->mock(StartDaemonScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andThrow(new ServerScriptException);
        });

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Bus::assertNotDispatched(UpdateDaemonStateJob::class);
        Event::assertDispatched(DaemonUpdatedEvent::class);
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
