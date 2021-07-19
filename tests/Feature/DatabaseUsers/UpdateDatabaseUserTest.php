<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\UpdateDatabaseUserAction;
use App\Collections\EloquentCollection;
use App\Http\Livewire\DatabaseUsers\DatabaseUsersIndexTable;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabaseUserPolicy;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class UpdateDatabaseUserTest extends AbstractFeatureTest
{
    public function test_database_user_can_be_updated()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();
        $user = $databaseUser->user;
        $server = $databaseUser->server;
        $databases = Database::factory()->for($server)->count(2)->create();
        $databaseUser->databases()->sync($databases);
        $newDatabases = Database::factory()->for($server)->count(2)->create();

        $this->mock(DatabaseUserPolicy::class, function (MockInterface $mock) use ($databaseUser, $server, $user) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('update')
                ->withArgs(fn(User $userArg, DatabaseUser $databaseUserArg) =>
                    $userArg->is($user) && $databaseUserArg->is($databaseUser))
                ->twice()
                ->andReturnTrue();
        });

        $this->actingAs($user);

        Livewire::test(DatabaseUsersIndexTable::class, ['server' => $server])
            /*
             * Update modal opening part
             */
            ->call('openModal', (string) $databaseUser->getKey())
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSet('password', '')
            ->assertSet('grantedDatabases',
                $databaseUser->databases
                    ->keyBy('id')
                    ->map(fn(Database $database) => true)
                    ->toArray()
            )
            ->assertDispatchedBrowserEvent('updating-database-user')
            /*
             * Actual updating part
             */
            ->set('password', 'foobarfoobar')
            ->set('grantedDatabases',
                $newDatabases
                    ->keyBy('id')
                    ->map(fn(Database $database) => true)
                    ->toArray()
            )
            ->call('update', Mockery::mock(
                UpdateDatabaseUserAction::class,
                function (MockInterface $mock) use ($databaseUser, $newDatabases) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (
                            DatabaseUser $databaseUserArg,
                            string $passwordArg,
                            EloquentCollection $grantedDatabasesArg,
                        ) use ($databaseUser, $newDatabases) {
                            return $databaseUserArg->is($databaseUser)
                                && $passwordArg === 'foobarfoobar'
                                && $grantedDatabasesArg->modelKeys() == $newDatabases->modelKeys();
                        })
                        ->once();
                }
            ))
            ->assertOk()
            ->assertHasNoErrors()
            ->assertSet('modalOpen', false)
            ->assertSet('password', '')
            ->assertSet('grantedDatabases', [])
            ->assertEmitted('database-user-stored')
            ->assertEmitted('database-updated');
    }
}
