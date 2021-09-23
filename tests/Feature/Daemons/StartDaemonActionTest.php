<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\StartDaemonAction;
use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\StartDaemonJob;
use App\Models\Daemon;
use App\States\Daemons\Starting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

// TODO: CRITICAL! CONTINUE.

class StartDaemonActionTest extends AbstractFeatureTest
{
    public function test_daemon_in_active_state_is_ignored()
    {
        $this->assertIgnored($this->execute('active'));
    }

    public function test_daemon_in_deleting_state_is_ignored()
    {
        $this->assertIgnored($this->execute('deleting'));
    }

    public function test_daemon_in_failed_state_gets_started()
    {
        $this->assertPerformed($this->execute('failed'));
    }

    public function test_daemon_in_restarting_state_is_ignored()
    {
        $this->assertIgnored($this->execute('restarting'));
    }

    public function test_daemon_in_starting_state_is_ignored()
    {
        $this->assertIgnored($this->execute('starting'));
    }

    public function test_daemon_in_stopped_state_gets_started()
    {
        $this->assertPerformed($this->execute('stopped'));
    }

    public function test_daemon_in_stopping_state_gets_started()
    {
        $this->assertPerformed($this->execute('stopping'));
    }

    /** Set up a test and execute the action. */
    protected function execute(string $state = 'starting'): Daemon
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        /** @var StartDaemonAction $action */
        $action = $this->app->make(StartDaemonAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($daemon);

        return $daemon;
    }

    protected function assertPerformed(Daemon $daemon): void
    {
        $daemon->refresh();

        $this->assertTrue($daemon->state->is(Starting::class));

        Bus::assertDispatched(StartDaemonJob::class);
        Event::assertDispatched(DaemonUpdatedEvent::class);

        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
    }

    protected function assertIgnored(Daemon $daemon): void
    {
        $sourceState = $daemon->state;

        $daemon->refresh();

        $this->assertTrue($daemon->state->is($sourceState));

        Bus::assertNotDispatched(StartDaemonJob::class);
        Event::assertNotDispatched(DaemonUpdatedEvent::class);

        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
    }
}
