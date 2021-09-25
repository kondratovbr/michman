<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\StopDaemonAction;
use App\Events\Daemons\DaemonCreatedEvent;
use App\Events\Daemons\DaemonDeletedEvent;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\StopDaemonJob;
use App\Models\Daemon;
use App\States\Daemons\Stopping;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StopDaemonActionTest extends AbstractFeatureTest
{
    public function test_daemon_in_active_state_gets_stopped()
    {
        $this->assertPerformed($this->execute('active'));
    }

    public function test_daemon_in_deleting_state_gets_ignored()
    {
        $this->assertIgnored($this->execute('deleting'));
    }

    public function test_daemon_in_failed_state_gets_ignored()
    {
        $this->assertIgnored($this->execute('failed'));
    }

    public function test_daemon_in_restarting_state_gets_stopped()
    {
        $this->assertPerformed($this->execute('restarting'));
    }

    public function test_daemon_in_starting_state_gets_stopped()
    {
        $this->assertPerformed($this->execute('starting'));
    }

    public function test_daemon_in_stopped_state_gets_ignored()
    {
        $this->assertIgnored($this->execute('stopped'));
    }

    public function test_daemon_in_stopping_state_gets_ignored()
    {
        $this->assertIgnored($this->execute('stopping'));
    }

    /** Set up a test and execute the action. */
    protected function execute(string $state = 'starting'): Daemon
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState($state)
            ->create();

        /** @var StopDaemonAction $action */
        $action = $this->app->make(StopDaemonAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($daemon);

        return $daemon;
    }

    protected function assertPerformed(Daemon $daemon): void
    {
        $daemon->refresh();

        $this->assertTrue($daemon->state->is(Stopping::class));

        Bus::assertDispatched(StopDaemonJob::class);
        Event::assertDispatched(DaemonUpdatedEvent::class);

        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
    }

    protected function assertIgnored(Daemon $daemon): void
    {
        $sourceState = $daemon->state;

        $daemon->refresh();

        $this->assertTrue($daemon->state->is($sourceState::class));

        Bus::assertNotDispatched(StopDaemonJob::class);
        Event::assertNotDispatched(DaemonUpdatedEvent::class);

        Event::assertNotDispatched(DaemonCreatedEvent::class);
        Event::assertNotDispatched(DaemonDeletedEvent::class);
    }
}
