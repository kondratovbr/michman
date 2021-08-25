<?php

namespace Tests\Feature\Pythons;

use App\Events\Pythons\PythonInstalledEvent;
use App\Jobs\Pythons\InstallPythonJob;
use App\Models\Python;
use App\Models\Server;
use App\Scripts\Root\Python3_9\InstallPythonScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class InstallPythonJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Python $python */
        $python = Python::factory([
            'version' => '3_9',
        ])->withServer()->installing()->create();
        $server = $python->server;

        $this->mockBind(InstallPythonScript::class, function (MockInterface $mock) use ($server) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(fn(Server $serverArg) => $serverArg->is($server))
                ->once()
                ->andReturn('3.9.2');
        });

        Bus::fake();
        Event::fake();

        $job = new InstallPythonJob($python);

        $this->assertEquals('servers', $job->queue);

        app()->call([$job, 'handle']);

        $this->assertDatabaseHas('pythons', [
            'server_id' => $server->getKey(),
            'version' => '3_9',
            'patch_version' => '3.9.2',
            'status' => Python::STATUS_INSTALLED,
        ]);

        $python->refresh();

        $this->assertTrue($python->isInstalled());

        Event::assertDispatched(PythonInstalledEvent::class);
    }
}
