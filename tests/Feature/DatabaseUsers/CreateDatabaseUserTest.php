<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\StoreDatabaseUserAction;
use App\DataTransferObjects\DatabaseUserData;
use App\Http\Livewire\DatabaseUsers\CreateDatabaseUserForm;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabaseUserPolicy;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateDatabaseUserTest extends AbstractFeatureTest
{
    public function test_database_user_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        $databases = Database::factory()
            ->for($server)
            ->count(3)
            ->create();

        $this->actingAs($user);

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('create')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->twice()
                ->andReturnTrue();
        });

        Livewire::test(CreateDatabaseUserForm::class, ['server' => $server])
            ->set('name', 'foobar')
            ->set('password', 'password')
            ->set('grantedDatabases', $databases->mapWithKeys(fn(Database $database) => [$database->id => true])->toArray())
            ->call('store', Mockery::mock(StoreDatabaseUserAction::class,
                function (MockInterface $mock) use ($server, $databases) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (
                            DatabaseUserData $databaseUserDataArg,
                            Server $serverArg,
                            Collection $grantedDatabasesArg
                        ) use ($server, $databases) {
                            return $databaseUserDataArg->name === 'foobar'
                                && $databaseUserDataArg->password === 'password'
                                && $serverArg->is($server)
                                && $grantedDatabasesArg->modelKeys() == $databases->modelKeys();
                        })
                        ->once()
                        ->andReturn(new DatabaseUser);
                }
            ))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertEmitted('database-user-stored')
            ->assertEmitted('database-updated')
            ->assertSet('name', null)
            ->assertSet('grantedDatabases', []);
    }

    public function test_database_user_with_empty_name_cannot_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        $this->actingAs($user);

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('create')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(CreateDatabaseUserForm::class, ['server' => $server])
            ->set('name', '')
            ->set('password', 'password')
            ->set('grantedDatabases', [])
            ->call('store', Mockery::mock(StoreDatabaseUserAction::class,
                function (MockInterface $mock) {
                    $mock->shouldNotHaveBeenCalled();
                }
            ))
            ->assertHasErrors('name');
    }

    public function test_databases_from_different_server_cannot_be_attached()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        /** @var Server $server */
        $anotherServer = Server::factory()
            ->withProvider()
            ->create();

        $databases = Database::factory()
            ->for($anotherServer)
            ->count(3)
            ->create();

        $this->actingAs($user);

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('create')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(CreateDatabaseUserForm::class, ['server' => $server])
            ->set('name', 'foobar')
            ->set('password', 'password')
            ->set('grantedDatabases', $databases->mapWithKeys(fn(Database $database) => [$database->id => true])->toArray())
            ->call('store', Mockery::mock(StoreDatabaseUserAction::class,
                function (MockInterface $mock) {
                    $mock->shouldNotHaveBeenCalled();
                }
            ))
            ->assertHasErrors(['grantedDatabases.0', 'grantedDatabases.1', 'grantedDatabases.2']);
    }
}
