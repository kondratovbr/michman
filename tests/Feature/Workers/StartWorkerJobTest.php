<?php

namespace Tests\Feature\Workers;

use App\Events\Workers\WorkerUpdatedEvent;
use App\Jobs\Workers\StartWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartWorkerScript;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class StartWorkerJobTest extends AbstractFeatureTest
{
    public function test_worker_gets_started()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState('starting')
            ->create();

        $job = new StartWorkerJob($worker);

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($worker, $server) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker, $server) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnTrue();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'active',
        ]);

        Event::assertDispatched(WorkerUpdatedEvent::class);
    }

    public function test_worker_failure_gets_handled()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState('starting')
            ->create();

        $job = new StartWorkerJob($worker);

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($worker, $server) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker, $server) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnFalse();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'failed',
        ]);

        Event::assertDispatched(WorkerUpdatedEvent::class);
    }

    public function test_script_failure_gets_handled()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState('starting')
            ->create();

        $job = new StartWorkerJob($worker);

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($worker, $server) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker, $server) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andThrow(new ServerScriptException);
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'failed',
        ]);

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

        $job = new StartWorkerJob($worker);

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($worker, $server) {
            $mock->shouldNotHaveBeenCalled();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => $state,
        ]);

        Event::assertNotDispatched(WorkerUpdatedEvent::class);
    }

    public function irrelevantStates(): array
    {
        return [
            ['active'],
            ['deleting'],
            ['failed'],
        ];
    }
}
