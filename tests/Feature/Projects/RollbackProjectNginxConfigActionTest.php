<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\RollbackProjectNginxConfigAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Deployment;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class RollbackProjectNginxConfigActionTest extends AbstractFeatureTest
{
    public function test_nginx_config_gets_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'nginx_config' => 'This is the old Nginx config.',
        ])->withProject()->create();
        $project = $deployment->project;

        /** @var RollbackProjectNginxConfigAction $action */
        $action = $this->app->make(RollbackProjectNginxConfigAction::class);

        Event::fake();

        $action->execute($project);

        $project->refresh();

        $this->assertEquals('This is the old Nginx config.', $project->nginxConfig);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
