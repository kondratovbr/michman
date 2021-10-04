<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectDeploymentBranchAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Project;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateProjectDeploymentBranchActionTest extends AbstractFeatureTest
{
    public function test_branch_gets_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();
        $project->update(['branch' => 'main']);

        /** @var UpdateProjectDeploymentBranchAction $action */
        $action = $this->app->make(UpdateProjectDeploymentBranchAction::class);

        Event::fake();

        $action->execute($project, 'prod');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'branch' => 'prod',
        ]);

        $project->refresh();

        $this->assertTrue($project->exists);
        $this->assertEquals('prod', $project->branch);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
