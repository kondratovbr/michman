<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\StoreProjectAction;
use App\DataTransferObjects\NewProjectData;
use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Events\Projects\ProjectCreatedEvent;
use App\Jobs\Servers\CreateUserOnServerJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Project;
use App\Models\Python;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreProjectActionTest extends AbstractFeatureTest
{
    public function test_project_gets_stored_and_database_and_user_get_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var StoreProjectAction $action */
        $action = $this->app->make(StoreProjectAction::class);

        Bus::fake();
        Event::fake();

        $action->execute(new NewProjectData(
            domain: 'foo.com',
            aliases: ['bar.com', 'baz.com'],
            type: 'django',
            python_version: '3_9',
            allow_sub_domains: true,
            create_database: true,
            db_name: 'foodb',
            create_db_user: true,
            db_user_name: 'foouser',
            db_user_password: 'password',
        ), $server);

        $this->assertDatabaseHas('projects', [
            'user_id' => $server->user->getKey(),
            'domain' => 'foo.com',
            'allow_sub_domains' => 1,
            'type' => 'django',
            'python_version' => '3_9',
            'repo' => null,
            'branch' => null,
            'package' => null,
            'root' => null,
            'use_deploy_key' => null,
            'requirements_file' => null,
            'environment' => null,
            'deploy_script' => null,
            'gunicorn_config' => null,
            'nginx_config' => null,
        ]);

        $server->fresh();

        $this->assertCount(1, $server->projects);
        $this->assertCount(1, $server->pythons);
        $this->assertCount(1, $server->databases);
        $this->assertCount(1, $server->databaseUsers);

        /** @var Project $project */
        $project = $server->projects->first();

        $this->assertCount(2, $project->aliases);
        $this->assertEquals('foo.com', $project->domain);
        $this->assertEquals('bar.com', $project->aliases[0]);
        $this->assertEquals('baz.com', $project->aliases[1]);
        $this->assertNotNull($project->database);
        $this->assertNotNull($project->databaseUser);

        /** @var Python $python */
        $python = $server->pythons->first();

        $this->assertEquals('3_9', $python->version);

        /** @var Database $database */
        $database = $project->database;

        $this->assertEquals('foodb', $database->name);
        $this->assertCount(1, $database->databaseUsers);
        $this->assertTrue($database->server->is($server));

        /** @var DatabaseUser $databaseUser */
        $databaseUser = $project->databaseUser;

        $this->assertEquals('foouser', $databaseUser->name);
        $this->assertCount(1, $databaseUser->databases);
        $this->assertTrue($databaseUser->server->is($server));

        $this->assertTrue($database->databaseUsers->first()->is($databaseUser));
        $this->assertTrue($databaseUser->databases->first()->is($database));

        Event::assertDispatched(ProjectCreatedEvent::class);
        Event::assertDispatched(DatabaseCreatedEvent::class);
        Event::assertDispatched(DatabaseUserCreatedEvent::class);

        Bus::assertDispatched(CreateUserOnServerJob::class);
    }
}
