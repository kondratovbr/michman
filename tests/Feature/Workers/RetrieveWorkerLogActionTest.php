<?php

namespace Tests\Feature\Workers;

use App\Actions\Workers\RetrieveWorkerLogAction;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\RetrieveWorkerLogScript;
use Mockery\MockInterface;
use App\Models\Worker;
use Tests\AbstractFeatureTest;
use RuntimeException;

class RetrieveWorkerLogActionTest extends AbstractFeatureTest
{
    public function test_logs_get_retrieved()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($project->servers->first())->create();

        $this->mock(RetrieveWorkerLogScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturn('Worker Log!');
        });

        /** @var RetrieveWorkerLogAction $action */
        $action = $this->app->make(RetrieveWorkerLogAction::class);

        $result = $action->execute($worker);

        $this->assertEquals('Worker Log!', $result);
    }

    public function test_retries_happen()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($project->servers->first())->create();

        $this->mock(RetrieveWorkerLogScript::class, function (MockInterface $mock) use ($worker) {
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

            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andThrow(new RuntimeException);

            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturn('Worker Log!');
        });

        /** @var RetrieveWorkerLogAction $action */
        $action = $this->app->make(RetrieveWorkerLogAction::class);

        $result = $action->execute($worker);

        $this->assertEquals('Worker Log!', $result);
    }

    public function test_failure_gets_handled()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Worker $worker */
        $worker = Worker::factory()->for($project)->for($project->servers->first())->create();

        $this->mock(RetrieveWorkerLogScript::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->twice()
                ->andThrow(new ServerScriptException);

            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Worker $workerArg,
                ) use ($worker) {
                    return $serverArg->is($worker->server)
                        && $workerArg->is($worker);
                })
                ->times(3)
                ->andThrow(new RuntimeException);
        });

        /** @var RetrieveWorkerLogAction $action */
        $action = $this->app->make(RetrieveWorkerLogAction::class);

        $result = $action->execute($worker);

        $this->assertFalse($result);
    }
}
