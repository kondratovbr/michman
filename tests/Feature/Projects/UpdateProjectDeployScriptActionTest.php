<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectDeployScriptAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Project;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateProjectDeployScriptActionTest extends AbstractFeatureTest
{
    public function test_deploy_script_gets_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();

        Event::fake();

        /** @var UpdateProjectDeployScriptAction $action */
        $action = $this->app->make(UpdateProjectDeployScriptAction::class);

        $action->execute($project, 'This is a new script!');

        $project->refresh();

        $this->assertEquals('This is a new script!', $project->deployScript);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
