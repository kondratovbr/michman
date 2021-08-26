<?php

namespace Tests\Feature\Projects;

use App\Events\Projects\ProjectUpdatedEvent;
use App\Http\Livewire\Projects\ProjectNginxConfigEditForm;
use App\Models\Deployment;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Event;
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

        Event::fake();

        Livewire::actingAs($user)->test(ProjectNginxConfigEditForm::class, ['project' => $project])
            ->set('nginxConfig', 'This is the new Nginx config!')
            ->call('update')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('nginxConfig', "This is the new Nginx config!\n");

        $project->refresh();

        $this->assertEquals("This is the new Nginx config!\n", $project->nginxConfig);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }

    public function test_project_nginx_config_can_be_rolled_back()
    {
        /** @var Deployment $deployment */
        $deployment = Deployment::factory([
            'nginx_config' => "This is the old Nginx config!\n",
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

        Livewire::actingAs($user)->test(ProjectNginxConfigEditForm::class, ['project' => $project])
            ->call('rollback')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('nginxConfig', "This is the old Nginx config!\n");

        $project->refresh();

        $this->assertEquals("This is the old Nginx config!\n", $project->nginxConfig);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }
}
