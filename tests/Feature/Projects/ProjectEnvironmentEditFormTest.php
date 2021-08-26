<?php

namespace Tests\Feature\Projects;

use App\Events\Projects\ProjectUpdatedEvent;
use App\Http\Livewire\Projects\ProjectEnvironmentEditForm;
use App\Models\Deployment;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ProjectEnvironmentEditFormTest extends AbstractFeatureTest
{
    public function test_project_environment_can_be_updated()
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

        Livewire::actingAs($user)->test(ProjectEnvironmentEditForm::class, ['project' => $project])
            ->set('environment', 'This is the new environment!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('environment', "This is the new environment!\n");

        $project->refresh();

        $this->assertEquals("This is the new environment!\n", $project->environment);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }

    public function test_project_environment_can_be_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'environment' => "This is the old environment!\n",
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

        Livewire::actingAs($user)->test(ProjectEnvironmentEditForm::class, ['project' => $project])
            ->call('rollback')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('environment', "This is the old environment!\n");

        $project->refresh();

        $this->assertEquals("This is the old environment!\n", $project->environment);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
