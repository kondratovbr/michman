<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\RollbackProjectDeployScriptAction;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Models\Deployment;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class RollbackProjectDeployScriptActionTest extends AbstractFeatureTest
{
    public function test_deploy_script_gets_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'deploy_script' => 'This is the deploy script.',
        ])->withProject()->create();
        $project = $deployment->project;

        /** @var RollbackProjectDeployScriptAction $action */
        $action = $this->app->make(RollbackProjectDeployScriptAction::class);

        Event::fake();

        $action->execute($project);

        $project->refresh();

        $this->assertEquals('This is the deploy script.', $project->deployScript);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
