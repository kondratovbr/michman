<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\InstallProjectRepoAction;
use App\DataTransferObjects\ProjectRepoDto;
use App\Events\Projects\ProjectUpdatedEvent;
use App\Jobs\DeploySshKeys\UploadDeploySshKeyToServerJob;
use App\Jobs\Projects\InstallProjectToServerJob;
use App\Jobs\ServerSshKeys\AddServerSshKeyToVcsJob;
use App\Jobs\ServerSshKeys\UploadServerSshKeyToServerJob;
use App\Models\Project;
use App\Models\Provider;
use App\Models\Server;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class InstallProjectRepoActionTest extends AbstractFeatureTest
{
    public function test_repo_gets_configured_and_installed_to_server_with_deployed_key()
    {
        [$project, $vcs] = $this->create();

        /** @var InstallProjectRepoAction $action */
        $action = $this->app->make(InstallProjectRepoAction::class);

        $data = [
            'root' => 'project_root',
            'repo' => 'user/repo',
            'branch' => 'main',
            'package' => 'django_app',
            'use_deploy_key' => true,
            'requirements_file' => 'requirements.txt',
        ];

        Bus::fake();
        Event::fake();

        $returned = $action->execute(
            $project,
            $vcs,
            ProjectRepoDto::fromArray($data),
            $project->servers->first(),
            true,
        );

        $this->assertTrue($returned instanceof Project);
        $this->assertTrue($project->is($returned));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'root' => 'project_root',
            'repo' => 'user/repo',
            'branch' => 'main',
            'package' => 'django_app',
            'use_deploy_key' => true,
            'requirements_file' => 'requirements.txt',
        ]);

        $project->refresh();

        $this->assertNotNull($project->vcsProvider);
        $this->assertTrue($project->vcsProvider->is($vcs));

        $this->assertNotNull($project->environment);
        $this->assertNotNull($project->deployScript);
        $this->assertNotNull($project->gunicornConfig);
        $this->assertNotNull($project->nginxConfig);

        Bus::assertChained([
            UploadDeploySshKeyToServerJob::class,
            InstallProjectToServerJob::class,
        ]);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }

    public function test_repo_gets_configured_and_installed_to_server_without_deployed_key()
    {
        [$project, $vcs] = $this->create();

        /** @var InstallProjectRepoAction $action */
        $action = $this->app->make(InstallProjectRepoAction::class);

        $data = [
            'root' => 'project_root',
            'repo' => 'user/repo',
            'branch' => 'main',
            'package' => 'django_app',
            'use_deploy_key' => false,
            'requirements_file' => 'requirements.txt',
        ];

        Bus::fake();
        Event::fake();

        $returned = $action->execute(
            $project,
            $vcs,
            ProjectRepoDto::fromArray($data),
            $project->servers->first(),
            true,
        );

        $this->assertTrue($returned instanceof Project);
        $this->assertTrue($project->is($returned));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'root' => 'project_root',
            'repo' => 'user/repo',
            'branch' => 'main',
            'package' => 'django_app',
            'use_deploy_key' => false,
            'requirements_file' => 'requirements.txt',
        ]);

        $project->refresh();

        $this->assertNotNull($project->vcsProvider);
        $this->assertTrue($project->vcsProvider->is($vcs));

        $this->assertNotNull($project->environment);
        $this->assertNotNull($project->deployScript);
        $this->assertNotNull($project->gunicornConfig);
        $this->assertNotNull($project->nginxConfig);

        Bus::assertChained([
            UploadServerSshKeyToServerJob::class,
            AddServerSshKeyToVcsJob::class,
            InstallProjectToServerJob::class,
        ]);

        Event::assertDispatched(ProjectUpdatedEvent::class);
    }

    protected function create(): array
    {
        /** @var VcsProvider $vcs */
        $vcs = VcsProvider::factory()->withUser()->create();

        $user = $vcs->user;

        /** @var Provider $provider */
        $provider = Provider::factory()->for($user, 'owner')->create();

        /** @var Server $server */
        $server = Server::factory()
            ->for($provider)
            ->create();

        /** @var Project $project */
        $project = Project::factory()
            ->for($vcs->user)
            ->hasAttached($server)
            ->create();

        return [$project, $vcs];
    }
}
