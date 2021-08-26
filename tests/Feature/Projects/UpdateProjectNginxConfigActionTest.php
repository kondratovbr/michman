<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectNginxConfigAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Project;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class UpdateProjectNginxConfigActionTest extends AbstractFeatureTest
{
    public function test_nginx_config_gets_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();

        Event::fake();

        /** @var UpdateProjectNginxConfigAction $action */
        $action = $this->app->make(UpdateProjectNginxConfigAction::class);

        $action->execute($project, 'This is a new Nginx config!');

        $project->refresh();

        $this->assertEquals('This is a new Nginx config!', $project->nginxConfig);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
