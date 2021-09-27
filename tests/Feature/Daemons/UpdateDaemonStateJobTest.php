<?php

namespace Tests\Feature\Daemons;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use App\Notifications\Daemons\DaemonFailedNotification;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\UpdateDaemonStateScript;
use App\States\Daemons\Active;
use App\States\Daemons\Failed;
use App\States\Daemons\Starting;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class UpdateDaemonStateJobTest extends AbstractFeatureTest
{
    /** @dataProvider eligibleStates */
    public function test_eligible_daemons_get_updated(string $state)
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        $this->mock(UpdateDaemonStateScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturn(new Active($daemon));
        });

        $job = new UpdateDaemonStateJob($daemon);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        if ($state === 'active')
            Event::assertNotDispatched(DaemonUpdatedEvent::class);
        else
            Event::assertDispatched(DaemonUpdatedEvent::class);

        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('daemons', [
            'id' => $daemon->id,
            'state' => 'active',
        ]);

        $daemon->refresh();

        $this->assertTrue($daemon->exists);
        $this->assertTrue($daemon->state->is(Active::class));
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_daemons_get_ignored(string $state)
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        $this->mock(UpdateDaemonStateScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldNotHaveBeenCalled();
        });

        $job = new UpdateDaemonStateJob($daemon);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

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

    public function test_job_gets_repeated_if_daemon_is_starting()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('starting')
            ->create();

        $this->mock(UpdateDaemonStateScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturn(new Starting($daemon));
        });

        $job = new UpdateDaemonStateJob($daemon);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        // TODO: Figure out how to test that job gets released here.
        $this->addWarning('The fact that job gets released isn\'t tested here yet.');

        Event::assertNotDispatched(DaemonUpdatedEvent::class);
        Event::assertNotDispatched(DaemonCreatedEvent::class);
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

    public function test_failure_gets_handled()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('starting')
            ->create();

        $this->mock(UpdateDaemonStateScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andThrowExceptions([new ServerScriptException]);
        });

        $job = new UpdateDaemonStateJob($daemon);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

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

    public function eligibleStates(): array
    {
        return [
            ['active'],
            ['failed'],
            ['restarting'],
            ['starting'],
        ];
    }

    public function ineligibleStates(): array
    {
        return [
            ['deleting'],
            ['stopped'],
            ['stopping'],
        ];
    }
}
