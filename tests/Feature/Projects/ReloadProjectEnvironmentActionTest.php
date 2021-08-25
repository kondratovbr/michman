<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\ReloadProjectEnvironmentAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Jobs\Projects\UpdateProjectEnvironmentOnServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Scripts\User\RetrieveProjectEnvironmentFromServerScript;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ReloadProjectEnvironmentActionTest extends AbstractFeatureTest
{
    public function test_action_logic()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();
        /** @var Server $server */
        $server = $project->servers->first();

        Bus::fake();
        Event::fake();

        $this->mockBind(RetrieveProjectEnvironmentFromServerScript::class, function (MockInterface $mock) use ($project, $server) {
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
                ->andReturn('This is a project environment!');
        });

        /** @var ReloadProjectEnvironmentAction $action */
        $action = $this->app->make(ReloadProjectEnvironmentAction::class);

        $newEnv = $action->execute($project);

        $this->assertEquals('This is a project environment!', $newEnv);

        $project->refresh();

        $this->assertEquals('This is a project environment!', $project->environment);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->queue() === 'servers'
                && $batch->jobs->count() === 1
                && $batch->jobs->first() instanceof UpdateProjectEnvironmentOnServerJob;
        });

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
