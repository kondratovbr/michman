<?php

namespace Tests\Feature\Workers;

use App\Actions\Workers\RestartWorkerAction;
use App\Events\Workers\WorkerUpdatedEvent;
use App\Jobs\Workers\RestartWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class RestartWorkerActionTest extends AbstractFeatureTest
{
    public function test_worker_gets_restarted()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState('active')
            ->create();

        /** @var RestartWorkerAction $action */
        $action = $this->app->make(RestartWorkerAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($worker);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'starting',
        ]);

        Bus::assertDispatched(RestartWorkerJob::class);
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

        /** @var RestartWorkerAction $action */
        $action = $this->app->make(RestartWorkerAction::class);

        Bus::fake();
        Event::fake();

        $action->execute($worker);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => $state,
        ]);

        Bus::assertNotDispatched(RestartWorkerJob::class);
        Event::assertNotDispatched(WorkerUpdatedEvent::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['deleting'],
        ];
    }
}
