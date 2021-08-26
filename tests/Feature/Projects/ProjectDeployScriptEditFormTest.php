<?php

namespace Tests\Feature\Projects;

use App\Events\Projects\ProjectUpdatedEvent;
use App\Http\Livewire\Projects\ProjectDeployScriptEditForm;
use App\Models\Deployment;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ProjectDeployScriptEditFormTest extends AbstractFeatureTest
{
    public function test_project_deploy_script_can_be_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        $user = $project->user;

        $this->mockBind(ProjectPolicy::class, function (MockInterface $mock) use ($user, $project) {
            $mock
                ->shouldReceive('update')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($user, $project) {
                    return $userArg->is($user)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturnTrue();
        });

        Event::fake();

        Livewire::actingAs($user)->test(ProjectDeployScriptEditForm::class, ['project' => $project])
            ->set('script', 'This is the new deploy script!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('script', "This is the new deploy script!\n");

        $project->refresh();

        $this->assertEquals("This is the new deploy script!\n", $project->deployScript);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }

    public function test_project_deploy_script_can_be_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'deploy_script' => "This is the old deploy script!\n",
        ])->withProject()->create();
        $project = $deployment->project;
        $user = $project->user;

        $this->mockBind(ProjectPolicy::class, function (MockInterface $mock) use ($user, $project) {
            $mock
                ->shouldReceive('update')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($user, $project) {
                    return $userArg->is($user)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturnTrue();
        });

        Event::fake();

        Livewire::actingAs($user)->test(ProjectDeployScriptEditForm::class, ['project' => $project])
            ->call('rollback')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('script', "This is the old deploy script!\n");

        $project->refresh();

        $this->assertEquals("This is the old deploy script!\n", $project->deployScript);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
