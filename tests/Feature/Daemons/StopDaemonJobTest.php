<?php

namespace Tests\Feature\Daemons;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\StopDaemonJob;
use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\Root\StopDaemonScript;
use App\States\Daemons\Stopped;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class StopDaemonJobTest extends AbstractFeatureTest
{
    public function test_eligible_daemon_gets_stopped()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('stopping')
            ->create();

        $job = new StopDaemonJob($daemon);

        $this->mock(StopDaemonScript::class, function (MockInterface $mock) use ($daemon) {
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

        Bus::assertNotDispatched(UpdateDaemonStateJob::class);
        Event::assertDispatched(DaemonUpdatedEvent::class);
        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('daemons', [
            'id' => $daemon->id,
            'state' => 'stopped',
        ]);

        $daemon->refresh();

        $this->assertTrue($daemon->exists);
        $this->assertTrue($daemon->state->is(Stopped::class));
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_daemons_get_ignored(string $state)
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        $job = new StopDaemonJob($daemon);

        $this->mock(StopDaemonScript::class, function (MockInterface $mock) use ($daemon) {
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
            ['starting'],
            ['stopped'],
        ];
    }
}
