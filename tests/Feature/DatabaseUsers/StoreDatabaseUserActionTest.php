<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\StoreDatabaseUserAction;
use App\DataTransferObjects\DatabaseUserDto;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserCreatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\DatabaseUsers\CreateDatabaseUserOnServerJob;
use App\Jobs\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreDatabaseUserActionTest extends AbstractFeatureTest
{
    public function test_database_user_gets_stored()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();

        $databases = Database::factory()
            ->for($server)
            ->count(3)
            ->create();

        /** @var StoreDatabaseUserAction $action */
        $action = $this->app->make(StoreDatabaseUserAction::class);

        Bus::fake();
        Event::fake();

        $databaseUser = $action->execute(
            new DatabaseUserDto(
                name: 'foobar',
                password: 'password',
            ),
            $server,
            $databases,
        );

        $this->assertDatabaseHas('database_users', [
            'id' => $databaseUser->id,
            'name' => $databaseUser->name,
        ]);

        $databaseUser->refresh();

        $this->assertEquals('password', $databaseUser->password);

        $this->assertEquals(2, $databaseUser->tasks);
        $this->assertTrue($databaseUser->hasTasks());

        $this->assertEquals(3, $databaseUser->databases->count());

        $databases = $databaseUser->databases;

        /** @var Database $database */
        foreach ($databases as $database) {
            $this->assertEquals(1, $database->tasks);
            $this->assertTrue($database->hasTasks());
        }

        Bus::assertChained([
            CreateDatabaseUserOnServerJob::class,
            GrantDatabaseUsersAccessToDatabasesJob::class,
        ]);

        Event::assertDispatched(fn(DatabaseUserCreatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 1);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 2);

        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $databases->modelKeys()[0], 1);
        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $databases->modelKeys()[1], 1);
        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $databases->modelKeys()[2], 1);
    }
}
