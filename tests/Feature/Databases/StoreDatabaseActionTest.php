<?php

namespace Tests\Feature\Databases;

use App\Actions\Databases\StoreDatabaseAction;
use App\DataTransferObjects\DatabaseData;
use App\Events\Databases\DatabaseCreatedEvent;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\Databases\CreateDatabaseOnServerJob;
use App\Jobs\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreDatabaseActionTest extends AbstractFeatureTest
{
    public function test_database_gets_stored()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        $databaseUsers = DatabaseUser::factory()
            ->for($server)
            ->count(3)
            ->create();

        /** @var StoreDatabaseAction $action */
        $action = $this->app->make(StoreDatabaseAction::class);

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

        $this->assertEquals(3, $database->databaseUsers->count());

        $databaseUsers = $database->databaseUsers;

        /** @var DatabaseUser $databaseUser */
        foreach ($databaseUsers as $databaseUser) {
            $this->assertEquals(1, $databaseUser->tasks);
            $this->assertTrue($databaseUser->hasTasks());
        }

        Bus::assertChained([
            CreateDatabaseOnServerJob::class,
            GrantDatabaseUsersAccessToDatabasesJob::class,
        ]);

        Event::assertDispatched(fn(DatabaseCreatedEvent $event) => $event->databaseKey === $database->getKey(), 1);
        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 2);

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[0], 1);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[1], 1);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[2], 1);
    }
}
