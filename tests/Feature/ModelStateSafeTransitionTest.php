<?php

namespace Tests\Feature;

use App\Events\Daemons\DaemonUpdatedEvent;
use App\Models\Daemon;
use App\States\Daemons\Active;
use App\States\Daemons\Restarting;
use App\States\Daemons\Stopped;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class ModelStateSafeTransitionTest extends AbstractFeatureTest
{
    public function test_valid_transition_is_performed()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('active')
            ->create();

        Event::fake();

        $model = $daemon->state->transitionToIfCan(Restarting::class);

        $this->assertTrue($model->is($daemon));

        $daemon->refresh();

        $this->assertTrue($daemon->state->is(Restarting::class));

        Event::assertDispatched(DaemonUpdatedEvent::class);
    }

    public function test_invalid_transition_is_ignored()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()
            ->withServer()
            ->inState('active')
            ->create();

        Event::fake();

        $model = $daemon->state->transitionToIfCan(Stopped::class);

        $this->assertTrue($model->is($daemon));

        $daemon->refresh();

        $this->assertTrue($daemon->state->is(Active::class));

        Event::assertNotDispatched(DaemonUpdatedEvent::class);
    }
}
