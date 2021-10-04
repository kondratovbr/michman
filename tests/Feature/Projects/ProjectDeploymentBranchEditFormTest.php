<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\UpdateProjectDeploymentBranchAction;
use App\Http\Livewire\Projects\ProjectDeploymentBranchEditForm;
use App\Models\Project;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class ProjectDeploymentBranchEditFormTest extends AbstractFeatureTest
{
    public function test_branch_gets_updated()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->repoInstalled()->create();
        $project->update(['branch' => 'main']);
        $user = $project->user;

        $this->mock(UpdateProjectDeploymentBranchAction::class, function (MockInterface $mock) use ($project) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Project $projectArg,
                    string $branchArg,
                ) use ($project) {
                    return $projectArg->is($project)
                        && $branchArg === 'prod';
                })
                ->once();
        });

        Livewire::actingAs($user)->test(ProjectDeploymentBranchEditForm::class, ['project' => $project])
            ->set('branch', 'prod')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertEmitted('project-updated');
    }
}
