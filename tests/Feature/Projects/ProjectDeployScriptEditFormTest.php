<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\ReloadProjectDeployScriptAction;
use App\Actions\Projects\UpdateProjectDeployScriptAction;
use App\Http\Livewire\Projects\ProjectDeployScriptEditForm;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
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

        $this->mockBind(UpdateProjectDeployScriptAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Project $projectArg,
                    string  $scriptArg,
                ) use ($project) {
                    return $projectArg->is($project)
                        && $scriptArg === "This is the new deploy script!\n";
                })
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectDeployScriptEditForm::class, ['project' => $project])
            ->set('script', 'This is the new deploy script!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_existing_project_deploy_script_can_be_loaded()
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

        $this->mockBind(ReloadProjectDeployScriptAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(fn(Project $projectArg) => $projectArg->is($project))
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectDeployScriptEditForm::class, ['project' => $project])
            ->call('reload')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }
}
