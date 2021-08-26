<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectGunicornConfigAction;
use App\Http\Livewire\Projects\ProjectGunicornConfigEditForm;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ProjectGunicornConfigEditFormTest extends AbstractFeatureTest
{
    public function test_project_gunicorn_config_can_be_updated()
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

        $this->mockBind(UpdateProjectGunicornConfigAction::class, function (MockInterface $mock) use ($project) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Project $projectArg,
                    string  $scriptArg,
                ) use ($project) {
                    return $projectArg->is($project)
                        && $scriptArg === "This is the new Gunicorn config!\n";
                })
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectGunicornConfigEditForm::class, ['project' => $project])
            ->set('gunicornConfig', 'This is the new Gunicorn config!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }
}
