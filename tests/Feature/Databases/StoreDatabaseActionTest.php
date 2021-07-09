<?php

namespace Tests\Feature\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\Actions\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesAction;
use App\Collections\EloquentCollection;
use App\DataTransferObjects\DatabaseData;
use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\Servers\CreateDatabaseOnServerJob;
use App\Jobs\Servers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class StoreDatabaseActionTest extends AbstractFeatureTest
{
    public function test_database_gets_stored()
    {
        /*
         * TODO: CRITICAL! CONTINUE! The problem is that with my mocking the grant job constructs before the database even gets stored in the DB, so the tasks counter doesn't get incremented.
         */

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

        $action = new StoreDatabaseAction(Mockery::mock(
            GrantDatabaseUsersAccessToDatabasesAction::class,
            function (MockInterface $mock) use ($databaseUsers) {
                $database = new Database([
                    'name' => 'foobar',
                ]);
                $database->id = Database::query()->max('id') + 1;

                $mock->shouldReceive('execute')
                    ->withArgs(function (BaseCollection $usersArg, BaseCollection $databasesArg) use ($databaseUsers) {
                        return $databasesArg->count() == 1
                            && $databasesArg->first()->name == 'foobar'
                            && $usersArg->count() == 3
                            && $usersArg->pluck('id')->toArray() == $databaseUsers->modelKeys();
                    })
                    ->once()
                    ->andReturn(new GrantDatabaseUsersAccessToDatabasesJob(
                        $databaseUsers,
                        new EloquentCollection([$database]),
                    ));
            }
        ));

        Bus::fake();
        Event::fake();

        $database = $action->execute(new DatabaseData(name: 'foobar'), $server, $databaseUsers);

        $this->assertDatabaseHas('databases', [
            'id' => $database->id,
            'name' => 'foobar',
        ]);

        $database->refresh();

        $this->assertEquals(2, $database->tasks);
        $this->assertTrue($database->hasTasks());

        Bus::assertChained([
            CreateDatabaseOnServerJob::class,
            GrantDatabaseUsersAccessToDatabasesJob::class,
        ]);

        Event::assertDispatched(fn(DatabaseCreatedEvent $event) => $event->databaseKey === $database->getKey());
        Event::assertDispatchedTimes(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 2);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[0]);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[1]);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[2]);
    }
}
