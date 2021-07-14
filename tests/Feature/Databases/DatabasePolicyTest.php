<?php

namespace Tests\Feature\Databases;

use App\Models\Database;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabasePolicy;
use Tests\AbstractFeatureTest;

class DatabasePolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->index($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_index_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->index($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_create_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->create($user, $server);

        $this->assertFalse($result);
    }

    public function test_create_action_with_server_without_installed_database()
    {
        /** @var Server $server */
        $server = Server::factory([
            'installed_database' => null,
        ])->withProvider()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_delete_action()
    {
        /** @var Database $database */
        $database = Database::factory()->withServer()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->delete($database->user, $database);

        $this->assertTrue($result);
    }

    public function test_delete_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Database $database */
        $database = Database::factory()->withServer()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->delete($user, $database);

        $this->assertFalse($result);
    }

    public function test_delete_action_with_database_that_has_active_tasks()
    {
        /** @var Database $database */
        $database = Database::factory([
            'tasks' => 1,
        ])->withServer()->create();

        /** @var DatabasePolicy $policy */
        $policy = $this->app->make(DatabasePolicy::class);

        $result = $policy->delete($database->user, $database);

        $this->assertFalse($result);
    }
}
