<?php

namespace Tests\Feature\Daemons;

use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\DeleteDaemonJob;
use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\Root\StopDaemonScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDaemonJobTest extends AbstractFeatureTest
{
    public function test_eligible_daemon_gets_deleted()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('deleting')
            ->create();

        $job = new DeleteDaemonJob($daemon);

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
        Event::assertDispatched(DaemonDeletedEvent::class);
        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonUpdatedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseMissing('daemons', [
            'id' => $daemon->id,
        ]);
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_daemons_get_ignored(string $state)
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        $job = new DeleteDaemonJob($daemon);

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
            ['failed'],
            ['restarting'],
            ['starting'],
            ['stopped'],
            ['stopping'],
        ];
    }
}
