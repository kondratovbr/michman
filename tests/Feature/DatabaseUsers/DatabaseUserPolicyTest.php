<?php

namespace Tests\Feature\DatabaseUsers;

use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabaseUserPolicy;
use Tests\AbstractFeatureTest;

class DatabaseUserPolicyTest extends AbstractFeatureTest
{
    public function test_successful_index_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->index($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_index_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->index($user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_create_action()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertTrue($result);
    }

    public function test_create_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->create($user, $server);

        $this->assertFalse($result);
    }

    public function test_create_action_with_server_without_installed_database()
    {
        /** @var Server $server */
        $server = Server::factory([
            'installed_database' => null,
        ])->withProvider()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->create($server->user, $server);

        $this->assertFalse($result);
    }

    public function test_successful_delete_action()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->delete($databaseUser->user, $databaseUser);

        $this->assertTrue($result);
    }

    public function test_delete_action_with_server_owned_by_different_user()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->delete($user, $databaseUser);

        $this->assertFalse($result);
    }

    public function test_delete_action_with_database_that_has_active_tasks()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory([
            'tasks' => 1,
        ])->withServer()->create();

        /** @var DatabaseUserPolicy $policy */
        $policy = $this->app->make(DatabaseUserPolicy::class);

        $result = $policy->delete($databaseUser->user, $databaseUser);

        $this->assertFalse($result);
    }
}
