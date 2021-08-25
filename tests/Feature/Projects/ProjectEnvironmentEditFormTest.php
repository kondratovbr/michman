<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\ReloadProjectEnvironmentAction;
use App\Actions\Projects\UpdateProjectEnvironmentAction;
use App\Http\Livewire\Projects\ProjectEnvironmentEditForm;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Livewire\Livewire;
use Mockery;
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

        $this->mockBind(UpdateProjectEnvironmentAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Project $projectArg,
                    string $envArg,
                ) use ($project) {
                    return $projectArg->is($project)
                        && $envArg === "This is the new environment!\n";
                })
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectEnvironmentEditForm::class, ['project' => $project])
            ->set('environment', 'This is the new environment!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_existing_project_environment_can_be_loaded()
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

        $this->mockBind(ReloadProjectEnvironmentAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(fn(Project $projectArg) => $projectArg->is($project))
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectEnvironmentEditForm::class, ['project' => $project])
            ->call('reload')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }
}
