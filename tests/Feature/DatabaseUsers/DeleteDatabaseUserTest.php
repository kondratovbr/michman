<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\DeleteDatabaseUserAction;
use App\Http\Livewire\DatabaseUsers\DatabaseUsersIndexTable;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabaseUserPolicy;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDatabaseUserTest extends AbstractFeatureTest
{
    public function test_database_user_can_be_deleted()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();
        $user = $databaseUser->user;
        $server = $databaseUser->server;
        $databases = Database::factory()->for($server)->count(2)->create();
        $databaseUser->databases()->sync($databases);

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($user, $server, $databaseUser) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($user, $server) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('delete')
                ->withArgs(function (
                    User $userArg,
                    DatabaseUser $dbUserArg,
                ) use ($user, $databaseUser) {
                    return $userArg->is($user)
                        && $dbUserArg->is($databaseUser);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(DeleteDatabaseUserAction::class, function (MockInterface $mock) use ($databaseUser) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(DatabaseUser $dbUserArg) => $dbUserArg->is($databaseUser))
                ->once();
        });

        Livewire::actingAs($user)->test(DatabaseUsersIndexTable::class, ['server' => $server])
            ->call('delete', $databaseUser->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_database_user_from_different_server_cannot_be_deleted()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();
        $user = $databaseUser->user;
        $server = $databaseUser->server;
        $databases = Database::factory()->for($server)->count(2)->create();
        $databaseUser->databases()->sync($databases);

        /** @var DatabaseUser $anotherDbUser */
        $anotherDbUser = DatabaseUser::factory()->withServer()->create();

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($user, $server, $databaseUser) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($user, $server) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(DeleteDatabaseUserAction::class, function (MockInterface $mock) use ($databaseUser) {
            $mock->shouldNotHaveBeenCalled();
        });

        Livewire::actingAs($user)->test(DatabaseUsersIndexTable::class, ['server' => $server])
            ->call('delete', $anotherDbUser->getKey())
            ->assertHasErrors('key');
    }

    public function test_database_user_with_empty_key_cannot_be_deleted()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();
        $user = $databaseUser->user;
        $server = $databaseUser->server;
        $databases = Database::factory()->for($server)->count(2)->create();
        $databaseUser->databases()->sync($databases);

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($user, $server, $databaseUser) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($user, $server) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(DeleteDatabaseUserAction::class, function (MockInterface $mock) use ($databaseUser) {
            $mock->shouldNotHaveBeenCalled();
        });

        Livewire::actingAs($user)->test(DatabaseUsersIndexTable::class, ['server' => $server])
            ->call('delete', '')
            ->assertHasErrors('key');
    }
}
