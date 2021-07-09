<?php

namespace Tests\Feature\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\DataTransferObjects\DatabaseData;
use App\Http\Livewire\Databases\CreateDatabaseForm;
use App\Models\Database;
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
            ->set('grantedUsers', [])
            ->call('store', Mockery::mock(StoreDatabaseAction::class,
                function (MockInterface $mock) use ($server) {
                    $mock->shouldReceive('execute')
                        ->withArgs(function (DatabaseData $databaseDataArg, Server $serverArg, Collection $grantedUsersArg) use ($server) {
                            return $databaseDataArg->name === 'foobar'
                                && $serverArg->is($server)
                                && $grantedUsersArg->isEmpty();
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
}
