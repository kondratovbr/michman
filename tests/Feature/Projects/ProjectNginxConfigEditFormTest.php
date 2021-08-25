<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\ReloadProjectNginxConfigAction;
use App\Actions\Projects\UpdateProjectNginxConfigAction;
use App\Http\Livewire\Projects\ProjectNginxConfigEditForm;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ProjectNginxConfigEditFormTest extends AbstractFeatureTest
{
    public function test_project_nginx_config_can_be_updated()
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

        $this->mockBind(UpdateProjectNginxConfigAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Project $projectArg,
                    string  $scriptArg,
                ) use ($project) {
                    return $projectArg->is($project)
                        && $scriptArg === "This is the new Nginx config!\n";
                })
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectNginxConfigEditForm::class, ['project' => $project])
            ->set('nginxConfig', 'This is the new Nginx config!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_existing_project_nginx_config_can_be_loaded()
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

        $this->mockBind(ReloadProjectNginxConfigAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(fn(Project $projectArg) => $projectArg->is($project))
                ->once()
                ->andReturn('This is the modified Nginx config!');
        });

        Livewire::actingAs($user)->test(ProjectNginxConfigEditForm::class, ['project' => $project])
            ->call('reload')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }
}
