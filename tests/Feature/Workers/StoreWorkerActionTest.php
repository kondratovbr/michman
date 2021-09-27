<?php

namespace Tests\Feature\Workers;

use App\Actions\Workers\StoreWorkerAction;
use App\DataTransferObjects\WorkerDto;
use App\Events\Workers\WorkerCreatedEvent;
use App\Jobs\Workers\StartWorkerJob;
use App\Models\Project;
use App\Models\Worker;
use App\States\Workers\Starting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreWorkerActionTest extends AbstractFeatureTest
{
    public function test_worker_gets_stored()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var StoreWorkerAction $action */
        $action = $this->app->make(StoreWorkerAction::class);

        $data = WorkerDto::fromArray([
            'type' => 'celery',
            'stop_seconds' => 10,
            'app' => 'django_app',
            'processes' => 2,
            'queues' => ['one', 'two'],
            'max_tasks_per_child' => 100,
            'max_memory_per_child' => 256,
        ]);

        Bus::fake();
        Event::fake();

        $action->execute($data, $project, $project->servers->first());

        $this->assertDatabaseHas('workers', $data->except('queues')->toArray([
            'server_id' => $project->servers->first()->id,
            'state' => 'starting',
        ]));

        $project->refresh();

        /** @var Worker $worker */
        $worker = $project->workers()->firstOrFail();

        $this->assertEquals(['one', 'two'], $worker->queues);
        $this->assertTrue($worker->state->is(Starting::class));

        Bus::assertDispatched(StartWorkerJob::class);
        Event::assertDispatched(WorkerCreatedEvent::class);
    }
}
