<?php

namespace Tests\Feature\Workers;

use App\Events\Workers\WorkerCreatedEvent;
use App\Events\Workers\WorkerDeletedEvent;
use App\Events\Workers\WorkerUpdatedEvent;
use App\Jobs\Workers\UpdateWorkerStateJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use App\Notifications\Workers\WorkerFailedNotification;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\UpdateWorkerStateScript;
use App\States\Workers\Active;
use App\States\Workers\Failed;
use App\States\Workers\Starting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class UpdateWorkerStateJobTest extends AbstractFeatureTest
{
    /** @dataProvider eligibleStates */
    public function test_eligible_workers_get_updated(string $state)
    {
        $worker = $this->worker($state);

        $this->mock(UpdateWorkerStateScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturn(new Active($worker));
        });

        $job = new UpdateWorkerStateJob($worker);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        if ($state === 'active')
            Event::assertNotDispatched(WorkerUpdatedEvent::class);
        else
            Event::assertDispatched(WorkerUpdatedEvent::class);

        Event::assertNotDispatched(WorkerCreatedEvent::class);
        Event::assertNotDispatched(WorkerDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'active',
        ]);

        $worker->refresh();

        $this->assertTrue($worker->exists);
        $this->assertTrue($worker->state->is(Active::class));
    }

    /** @dataProvider ineligibleStates */
    public function test_ineligible_workers_get_ignored(string $state)
    {
        $worker = $this->worker($state);

        $this->mock(UpdateWorkerStateScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldNotHaveBeenCalled();
        });

        $job = new UpdateWorkerStateJob($worker);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Event::assertNotDispatched(WorkerCreatedEvent::class);
        Event::assertNotDispatched(WorkerUpdatedEvent::class);
        Event::assertNotDispatched(WorkerDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => $state,
        ]);

        $worker->refresh();

        $this->assertTrue($worker->exists);
        $this->assertEquals($state, $worker->state->getValue());
    }

    public function test_job_gets_repeated_if_worker_is_starting()
    {
        $worker = $this->worker('starting');

        $this->mock(UpdateWorkerStateScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturn(new Starting($worker));
        });

        $job = new UpdateWorkerStateJob($worker);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Event::assertNotDispatched(WorkerCreatedEvent::class);
        Event::assertNotDispatched(WorkerUpdatedEvent::class);
        Event::assertNotDispatched(WorkerDeletedEvent::class);
        Notification::assertNothingSent();

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'starting',
        ]);

        $worker->refresh();

        $this->assertTrue($worker->exists);
        $this->assertTrue($worker->state->is(Starting::class));
    }

    public function test_failure_gets_handled()
    {
        $worker = $this->worker('starting');

        $this->mock(UpdateWorkerStateScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andThrow(new ServerScriptException);
        });

        $job = new UpdateWorkerStateJob($worker);

        Bus::fake();
        Event::fake();
        Notification::fake();

        $this->app->call([$job, 'handle']);

        Event::assertDispatched(WorkerUpdatedEvent::class);

        Event::assertNotDispatched(WorkerCreatedEvent::class);
        Event::assertNotDispatched(WorkerDeletedEvent::class);
        Notification::assertSentTo($worker->user, WorkerFailedNotification::class);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'state' => 'failed',
        ]);

        $worker->refresh();

        $this->assertTrue($worker->exists);
        $this->assertTrue($worker->state->is(Failed::class));
    }

    public function eligibleStates(): array
    {
        return [
            ['active'],
            ['starting'],
            ['failed'],
        ];
    }

    public function ineligibleStates(): array
    {
        return [
            ['deleting'],
        ];
    }

    protected function worker(string $state): Worker
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Worker $worker */
        $worker = Worker::factory()
            ->for($server)
            ->for($project)
            ->inState($state)
            ->create();

        return $worker;
    }
}
