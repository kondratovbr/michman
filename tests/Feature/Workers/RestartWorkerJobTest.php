<?php

namespace Tests\Feature\Workers;

use App\Events\Workers\WorkerUpdatedEvent;
use App\Jobs\Workers\RestartWorkerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use App\Models\WorkerSshKey;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\StartWorkerScript;
use App\Scripts\Root\StopWorkerScript;
use App\States\Workers\Active;
use App\States\Workers\Failed;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;
use Tests\Feature\Traits\MocksSshSessions;

class RestartWorkerJobTest extends AbstractFeatureTest
{
    use MocksSshSessions;

    public function test_worker_gets_restarted()
    {
        $worker = $this->worker('starting');
        $server = $worker->server;

        $job = new RestartWorkerJob($worker);

        $this->mockSftp();

        $this->mock(StopWorkerScript::class, function (MockInterface $mock) use ($server, $worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                    SFTP $sshArg,
                ) use ($server, $worker) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once();
        });

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($server, $worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                    SFTP $sshArg,
                ) use ($server, $worker) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnTrue();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $worker->refresh();

        $this->assertTrue($worker->state->is(Active::class));

        Event::assertDispatched(WorkerUpdatedEvent::class);
    }

    public function test_worker_failure_gets_handled()
    {
        $worker = $this->worker('starting');
        $server = $worker->server;

        $job = new RestartWorkerJob($worker);

        $this->mockSftp();

        $this->mock(StopWorkerScript::class, function (MockInterface $mock) use ($server, $worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                    SFTP $sshArg,
                ) use ($server, $worker) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once();
        });

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($server, $worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                    SFTP $sshArg,
                ) use ($server, $worker) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnFalse();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $worker->refresh();

        $this->assertTrue($worker->state->is(Failed::class));

        Event::assertDispatched(WorkerUpdatedEvent::class);
    }

    public function test_script_failure_gets_handled()
    {
        $worker = $this->worker('starting');
        $server = $worker->server;

        $job = new RestartWorkerJob($worker);

        $this->mockSftp();

        $this->mock(StopWorkerScript::class, function (MockInterface $mock) use ($server, $worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                    SFTP $sshArg,
                ) use ($server, $worker) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once();
        });

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) use ($server, $worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                    SFTP $sshArg,
                ) use ($server, $worker) {
                    return $serverArg->is($server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andThrow(new ServerScriptException);
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $worker->refresh();

        $this->assertTrue($worker->state->is(Failed::class));

        Event::assertDispatched(WorkerUpdatedEvent::class);
    }

    /** @dataProvider irrelevantStates */
    public function test_irrelevant_workers_get_ignored(string $state)
    {
        $worker = $this->worker($state);

        $job = new RestartWorkerJob($worker);

        $this->mockSftp();

        $this->mock(StopWorkerScript::class, function (MockInterface $mock) {
            $mock->shouldNotHaveBeenCalled();
        });

        $this->mock(StartWorkerScript::class, function (MockInterface $mock) {
            $mock->shouldNotHaveBeenCalled();
        });

        Event::fake();

        $this->app->call([$job, 'handle']);

        $worker->refresh();

        $this->assertEquals($state, $worker->state->getValue());

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

    protected function worker(string $state): Worker
    {
        /** @var Server $server */
        $server = (WorkerSshKey::factory()->withServer()->create())->server;
        /** @var Project $project */
        $project = Project::factory()
            ->for($server->user)
            ->hasAttached($server)
            ->create();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($server)
            ->inState($state)
            ->create();

        return $worker;
    }
}
