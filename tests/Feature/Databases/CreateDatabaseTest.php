<?php

namespace Tests\Feature\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\DataTransferObjects\DatabaseData;
use App\Http\Livewire\Databases\CreateDatabaseForm;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabasePolicy;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateDatabaseTest extends AbstractFeatureTest
{
    public function test_database_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        $databaseUsers = DatabaseUser::factory()
            ->for($server)
            ->count(3)
            ->create();

        $this->actingAs($user);

        $this->mock(DatabasePolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('create')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(CreateDatabaseForm::class, ['server' => $server])
            ->set('name', 'foobar')
            ->set('grantedUsers', $databaseUsers->mapWithKeys(fn(DatabaseUser $databaseUser) => [$databaseUser->id => true])->toArray())
            ->call('store', Mockery::mock(StoreDatabaseAction::class,
                function (MockInterface $mock) use ($server, $databaseUsers) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (
                            DatabaseData $databaseDataArg,
                            Server $serverArg,
                            Collection $grantedUsersArg
                        ) use ($server, $databaseUsers) {
                            return $databaseDataArg->name === 'foobar'
                                && $serverArg->is($server)
                                && $grantedUsersArg->modelKeys() == $databaseUsers->modelKeys();
                        })
                        ->once()
                        ->andReturn(new Database);
                }
            ))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertEmitted('database-stored')
            ->assertEmitted('database-user-updated')
            ->assertSet('name', null)
            ->assertSet('grantedUsers', []);
    }

    public function test_database_with_empty_name_cannot_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        $this->actingAs($user);

        $this->mock(DatabasePolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(CreateDatabaseForm::class, ['server' => $server])
            ->set('name', '')
            ->set('grantedUsers', [])
            ->call('store', Mockery::mock(StoreDatabaseAction::class,
                function (MockInterface $mock) {
                    $mock->shouldNotHaveBeenCalled();
                }
            ))
            ->assertHasErrors('name');
    }

    public function test_database_users_from_different_server_cannot_be_attached()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        /** @var Server $anotherServer */
        $anotherServer = Server::factory()
            ->withProvider()
            ->create();

        $databaseUsers = DatabaseUser::factory()
            ->for($anotherServer)
            ->count(3)
            ->create();

        $this->actingAs($user);

        $this->mock(DatabasePolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(CreateDatabaseForm::class, ['server' => $server])
            ->set('name', 'foobar')
            ->set('grantedUsers', $databaseUsers->mapWithKeys(fn(DatabaseUser $databaseUser) => [$databaseUser->id => true])->toArray())
            ->call('store', Mockery::mock(StoreDatabaseAction::class,
                function (MockInterface $mock) {
                    $mock->shouldNotHaveBeenCalled();
                }
            ))
            ->assertHasErrors(['grantedUsers.0', 'grantedUsers.1', 'grantedUsers.2']);
    }
}
