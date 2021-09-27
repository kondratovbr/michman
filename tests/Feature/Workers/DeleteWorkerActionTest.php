<?php

namespace Tests\Feature\Workers;

use App\Actions\Workers\DeleteWorkerAction;
use App\Events\Workers\WorkerUpdatedEvent;
use App\Jobs\Workers\DeleteWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class DeleteWorkerActionTest extends AbstractFeatureTest
{
    public function test_job_gets_dispatched()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState('active')
            ->create();

        /** @var DeleteWorkerAction $action */
        $action = $this->app->make(DeleteWorkerAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($worker);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'deleting',
        ]);

        Bus::assertDispatched(DeleteWorkerJob::class);
        Event::assertDispatched(WorkerUpdatedEvent::class);
    }

    /** @dataProvider irrelevantStates */
    public function test_irrelevant_workers_get_ignored(string $state)
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState($state)
            ->create();

        /** @var DeleteWorkerAction $action */
        $action = $this->app->make(DeleteWorkerAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($worker);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => $state,
        ]);

        Bus::assertNotDispatched(DeleteWorkerJob::class);
        Event::assertNotDispatched(WorkerUpdatedEvent::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['deleting'],
        ];
    }
}
