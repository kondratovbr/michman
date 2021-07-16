<?php

namespace Tests\Feature\Pythons;

use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\PythonData;
use App\Jobs\Servers\CreatePythonJob;
use App\Models\Python;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreatePythonJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        Bus::fake();
        Event::fake();

        $job = new CreatePythonJob($server, '3_9');

        $this->assertEquals('default', $job->queue);

        $this->mock(StorePythonAction::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(PythonData $dataArg, Server $serverArg) =>
                    $dataArg->version === '3_9'
                    && $serverArg->is($server)
                )
                ->once()
                ->andReturn(new Python);
        });

        $this->app->call([$job, 'handle']);
    }
}
