<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\RetrieveDaemonLogAction;
use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Root\RetrieveDaemonLogScript;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use RuntimeException;

class RetrieveDaemonLogActionTest extends AbstractFeatureTest
{
    public function test_logs_get_retrieved()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        $this->mock(RetrieveDaemonLogScript::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Daemon $daemonArg,
                ) use ($daemon) {
                    return $serverArg->is($daemon->server)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturn('Log!');
        });

        /** @var RetrieveDaemonLogAction $action */
        $action = $this->app->make(RetrieveDaemonLogAction::class);

        $result = $action->execute($daemon);

        $this->assertEquals('Log!', $result);
    }

    public function test_retries_happen()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        $this->mock(RetrieveDaemonLogScript::class, function (MockInterface $mock) use ($daemon) {
            $validateArgs = function (
                Server $serverArg,
                Daemon $daemonArg,
            ) use ($daemon) {
                return $serverArg->is($daemon->server)
                    && $daemonArg->is($daemon);
            };

            $mock->shouldReceive('execute')
                ->withArgs($validateArgs)
                ->once()
                ->andThrow(new RuntimeException);

            $mock->shouldReceive('execute')
                ->withArgs($validateArgs)
                ->once()
                ->andThrow(new ServerScriptException);

            $mock->shouldReceive('execute')
                ->withArgs($validateArgs)
                ->once()
                ->andReturn('Log!');
        });

        /** @var RetrieveDaemonLogAction $action */
        $action = $this->app->make(RetrieveDaemonLogAction::class);

        $result = $action->execute($daemon);

        $this->assertEquals('Log!', $result);
    }

    public function test_failure_gets_handled()
    {
        /** @var Daemon $daemon */
        $daemon = Daemon::factory()->withServer()->create();

        $this->mock(RetrieveDaemonLogScript::class, function (MockInterface $mock) use ($daemon) {
            $validateArgs = function (
                Server $serverArg,
                Daemon $daemonArg,
            ) use ($daemon) {
                return $serverArg->is($daemon->server)
                    && $daemonArg->is($daemon);
            };

            $mock->shouldReceive('execute')
                ->withArgs($validateArgs)
                ->times(2)
                ->andThrow(new RuntimeException);

            $mock->shouldReceive('execute')
                ->withArgs($validateArgs)
                ->times(3)
                ->andThrow(new ServerScriptException);
        });

        /** @var RetrieveDaemonLogAction $action */
        $action = $this->app->make(RetrieveDaemonLogAction::class);

        $result = $action->execute($daemon);

        $this->assertFalse($result);
    }
}
