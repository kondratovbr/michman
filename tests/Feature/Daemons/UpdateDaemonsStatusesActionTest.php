<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\UpdateDaemonsStatusesAction;
use App\Events\Daemons\DaemonUpdatedEvent;
use App\Jobs\Daemons\UpdateDaemonStateJob;
use App\Models\Daemon;
use App\Models\Server;
use Illuminate\Bus\PendingBatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateDaemonsStatusesActionTest extends AbstractFeatureTest
{
    public function test_jobs_get_dispatched()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var Collection $daemons */
        $daemons = Daemon::factory()
            ->for($server)
            ->count(3)
            ->create();

        /** @var UpdateDaemonsStatusesAction $action */
        $action = $this->app->make(UpdateDaemonsStatusesAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($server);

        Bus::assertBatched(function (PendingBatch $batch) {
            foreach ($batch->jobs as $job) {
                if (! $job instanceof UpdateDaemonStateJob)
                    return false;
            }

            return $batch->jobs->count() === 3
                && $batch->allowsFailures()
                && $batch->queue() === 'servers';
        });

        Event::assertNotDispatched(DaemonUpdatedEvent::class);
    }
}
