<?php

namespace Tests\Feature;

use App\Actions\WorkerSshKeys\CreateWorkerSshKeyAction;
use App\Jobs\Servers\CreateWorkerSshKeyForServerJob;
use App\Models\Server;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateWorkerSshKeyForServerJobTest extends AbstractFeatureTest
{
    public function test_job_has_correct_parameters_and_calls_action()
    {
        $server = Server::factory()->withProvider()->create();

        $job = new CreateWorkerSshKeyForServerJob($server);

        $this->assertEquals('default', $job->queue);

        $this->mock(CreateWorkerSshKeyAction::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('execute')->withAnyArgs()->once();
        });

        app()->call([$job, 'handle']);
    }
}
