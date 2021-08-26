<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\RollbackProjectGunicornConfigAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Deployment;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class RollbackProjectGunicornConfigActionTest extends AbstractFeatureTest
{
    public function test_gunicorn_config_gets_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'gunicorn_config' => 'This is the old Gunicorn config.',
        ])->withProject()->create();
        $project = $deployment->project;

        /** @var RollbackProjectGunicornConfigAction $action */
        $action = $this->app->make(RollbackProjectGunicornConfigAction::class);

        Event::fake();

        $action->execute($project);

        $project->refresh();

        $this->assertEquals('This is the old Gunicorn config.', $project->gunicornConfig);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
