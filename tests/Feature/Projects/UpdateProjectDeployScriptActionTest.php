<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectDeployScriptAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Jobs\Projects\UpdateProjectDeployScriptOnServerJob;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateProjectDeployScriptActionTest extends AbstractFeatureTest
{
    public function test_action_logic()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();
        /** @var Server $server */
        $server = $project->servers->first();

        Bus::fake();
        Event::fake();

        /** @var UpdateProjectDeployScriptAction $action */
        $action = $this->app->make(UpdateProjectDeployScriptAction::class);

        $action->execute($project, 'This is a new script!');

        $project->refresh();

        $this->assertEquals('This is a new script!', $project->deployScript);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->queue() === 'servers'
                && $batch->jobs->count() === 1
                && $batch->jobs->first() instanceof UpdateProjectDeployScriptOnServerJob;
        });

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
