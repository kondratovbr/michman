<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\ReloadProjectNginxConfigAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Jobs\Projects\UpdateProjectNginxConfigOnServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\Root\RetrieveProjectNginxConfigFromServerScript;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ReloadProjectNginxConfigActionTest extends AbstractFeatureTest
{
    public function test_action_logic()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();
        /** @var Server $server */
        $server = $project->servers->first();

        Bus::fake();
        Event::fake();

        $this->mockBind(RetrieveProjectNginxConfigFromServerScript::class, function (MockInterface $mock) use ($project, $server) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    Project $projectArg,
                ) use ($project, $server) {
                    return $serverArg->is($server)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturn('This is a new Nginx config!');
        });

        /** @var ReloadProjectNginxConfigAction $action */
        $action = $this->app->make(ReloadProjectNginxConfigAction::class);

        $resultConfig = $action->execute($project);

        $this->assertEquals('This is a new Nginx config!', $resultConfig);

        $project->refresh();

        $this->assertEquals('This is a new Nginx config!', $project->nginxConfig);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->queue() === 'servers'
                && $batch->jobs->count() === 1
                && $batch->jobs->first() instanceof UpdateProjectNginxConfigOnServerJob;
        });

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
