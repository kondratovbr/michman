<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\RollbackProjectEnvironmentAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Deployment;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class RollbackProjectEnvironmentActionTest extends AbstractFeatureTest
{
    public function test_environment_gets_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'environment' => 'This is the old environment.',
        ])->withProject()->create();
        $project = $deployment->project;

        /** @var RollbackProjectEnvironmentAction $action */
        $action = $this->app->make(RollbackProjectEnvironmentAction::class);

        Event::fake();

        $action->execute($project);

        $project->refresh();

        $this->assertEquals('This is the old environment.', $project->environment);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
