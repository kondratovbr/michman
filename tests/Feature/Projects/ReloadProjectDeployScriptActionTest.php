<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\ReloadProjectDeployScriptAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Jobs\Projects\UpdateProjectDeployScriptOnServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\User\RetrieveProjectDeployScriptFromServerScript;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ReloadProjectDeployScriptActionTest extends AbstractFeatureTest
{
    public function test_action_logic()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();
        /** @var Server $server */
        $server = $project->servers->first();

        Bus::fake();
        Event::fake();

        $this->mockBind(RetrieveProjectDeployScriptFromServerScript::class, function (MockInterface $mock) use ($project, $server) {
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
                ->andReturn('This is a script!');
        });

        /** @var ReloadProjectDeployScriptAction $action */
        $action = $this->app->make(ReloadProjectDeployScriptAction::class);

        $resultScript = $action->execute($project);

        $this->assertEquals('This is a script!', $resultScript);

        $project->refresh();

        $this->assertEquals('This is a script!', $project->deployScript);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->queue() === 'servers'
                && $batch->jobs->count() === 1
                && $batch->jobs->first() instanceof UpdateProjectDeployScriptOnServerJob;
        });

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
