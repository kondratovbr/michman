<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectGunicornConfigAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Project;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateProjectGunicornConfigActionTest extends AbstractFeatureTest
{
    public function test_gunicorn_config_gets_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();

        Event::fake();

        /** @var UpdateProjectGunicornConfigAction $action */
        $action = $this->app->make(UpdateProjectGunicornConfigAction::class);

        $action->execute($project, 'This is the new Gunicorn config!');

        $project->refresh();

        $this->assertEquals('This is the new Gunicorn config!', $project->gunicornConfig);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
