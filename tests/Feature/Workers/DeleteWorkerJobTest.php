<?php

namespace Tests\Feature\Workers;

use App\Events\Workers\WorkerDeletedEvent;
use App\Jobs\Workers\DeleteWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use App\Scripts\Root\StopWorkerScript;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteWorkerJobTest extends AbstractFeatureTest
{
    public function test_worker_gets_deleted()
    {
        $worker = $this->worker('deleting');

        $job = new DeleteWorkerJob($worker);

        $this->mock(StopWorkerScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseMissing('workers', [
            'id' => $worker->id,
        ]);

        Event::assertDispatched(WorkerDeletedEvent::class);
    }

    /** @dataProvider irrelevantStates */
    public function test_irrelevant_workers_get_ignored(string $state)
    {
        $worker = $this->worker($state);

        $job = new DeleteWorkerJob($worker);

        $this->mock(StopWorkerScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldNotHaveBeenCalled();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => $state,
        ]);

        Event::assertNotDispatched(WorkerDeletedEvent::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['active'],
            ['failed'],
            ['starting'],
        ];
    }

    protected function worker(string $state): Worker
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();

        /** @var Worker $worker */
        $worker = Worker::factory()
            ->for($project->servers->first())
            ->for($project)
            ->inState($state)
            ->create();

        return $worker;
    }
}
