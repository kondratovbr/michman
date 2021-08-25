<?php

namespace Tests\Feature\Projects;

use App\Jobs\Projects\UpdateProjectDeployScriptOnServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\User\UpdateProjectDeployScriptOnServerScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class UpdateProjectDeployScriptOnServerJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();

        Bus::fake();
        Event::fake();

        $job = new UpdateProjectDeployScriptOnServerJob($server, $project);

        $this->assertEquals('servers', $job->queue);

        $this->mockBind(UpdateProjectDeployScriptOnServerScript::class, function (MockInterface $mock) use ($project, $server) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Project $projectArg,
                ) use ($project, $server) {
                    return $serverArg->is($server)
                        && $projectArg->is($project);
                })
                ->once();
        });

        app()->call([$job, 'handle']);
    }
}
