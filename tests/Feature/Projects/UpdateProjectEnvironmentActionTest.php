<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectEnvironmentAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Project;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateProjectEnvironmentActionTest extends AbstractFeatureTest
{
    public function test_environment_gets_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();

        Event::fake();

        /** @var UpdateProjectEnvironmentAction $action */
        $action = $this->app->make(UpdateProjectEnvironmentAction::class);

        $action->execute($project, 'This is the new env!');

        $project->refresh();

        $this->assertEquals('This is the new env!', $project->environment);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
