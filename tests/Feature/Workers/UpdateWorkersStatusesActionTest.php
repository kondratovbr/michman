<?php

namespace Tests\Feature\Workers;

use App\Actions\Workers\UpdateWorkersStatusesAction;
use App\Events\Workers\WorkerUpdatedEvent;
use App\Jobs\Workers\UpdateWorkerStateJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use Illuminate\Bus\PendingBatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateWorkersStatusesActionTest extends AbstractFeatureTest
{
    public function test_jobs_get_dispatched()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Collection $workers */
        $workers = Worker::factory()->for($project)->for($server)->count(3)->create();
        /** @var Worker $worker */
        $worker = $workers->first();
        $user = $project->user;

        /** @var UpdateWorkersStatusesAction $action */
        $action = $this->app->make(UpdateWorkersStatusesAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($project);

        Bus::assertBatched(function (PendingBatch $batch) {
            foreach ($batch->jobs as $job) {
                if (! $job instanceof UpdateWorkerStateJob)
                    return false;
            }

            return $batch->jobs->count() === 3
                && $batch->allowsFailures()
                && $batch->queue() === 'servers';
        });

        Event::assertNotDispatched(WorkerUpdatedEvent::class);
    }
}
