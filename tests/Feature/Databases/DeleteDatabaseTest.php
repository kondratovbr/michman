<?php

namespace Tests\Feature\Databases;

use App\Actions\Databases\DeleteDatabaseAction;
use App\Http\Livewire\Databases\DatabasesIndexTable;
use App\Models\Database;
use App\Models\Server;
use App\Models\User;
use App\Policies\DatabasePolicy;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDatabaseTest extends AbstractFeatureTest
{
    // TODO: CRITICAL! Don't forget to update this to include the confirmation dialog after it is made.

    public function test_database_can_be_deleted()
    {
        /** @var Database $database */
        $database = Database::factory()->withServer()->create();
        $server = $database->server;
        $user = $database->user;

        $this->actingAs($user);

        $this->mock(DatabasePolicy::class, function (MockInterface $mock) use ($user, $server, $database) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('delete')
                ->withArgs(fn(User $userArg, Database $databaseArg) => $userArg->is($user) && $databaseArg->is($database))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(DatabasesIndexTable::class, ['server' => $server])
            ->call('delete',
                Mockery::mock(DeleteDatabaseAction::class,
                    function (MockInterface $mock) use ($server, $database) {
                        $mock->shouldReceive('execute')
                            ->withArgs(fn(Database $databaseArg) => $databaseArg->is($database))
                            ->once();
                    }
                ),
                (string) $database->id,
            )
            ->assertOk()
            ->assertHasNoErrors();
    }

    public function test_database_from_different_server_cannot_be_deleted()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();
        $user = $server->user;

        /** @var Server $anotherServer */
        $anotherServer = Server::factory()->for($server->provider)->create();

        /** @var Database $database */
        $database = Database::factory()->for($anotherServer)->create();

        $this->actingAs($user);

        $this->mock(DatabasePolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(DatabasesIndexTable::class, ['server' => $server])
            ->call('delete',
                Mockery::mock(DeleteDatabaseAction::class,
                    function (MockInterface $mock) {
                        $mock->shouldNotHaveBeenCalled();
                    }
                ),
                (string) $database->id,
            )
            ->assertHasErrors('key');
    }

    public function test_database_with_empty_key_cannot_be_deleted()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        $user = $server->user;

        /** @var Server $anotherServer */
        $anotherServer = Server::factory()->for($server->provider)->create();

        /** @var Database $database */
        $database = Database::factory()->for($anotherServer)->create();

        $this->actingAs($user);

        $this->mock(DatabasePolicy::class, function (MockInterface $mock) use ($user, $server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) => $userArg->is($user) && $serverArg->is($server))
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(DatabasesIndexTable::class, ['server' => $server])
            ->call('delete',
                Mockery::mock(DeleteDatabaseAction::class,
                    function (MockInterface $mock) {
                        $mock->shouldNotHaveBeenCalled();
                    }
                ),
                '',
            )
            ->assertHasErrors('key');
    }
}
