<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\GrantDatabaseUsersAccessToDatabasesAction;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\Servers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class GrantDatabaseUsersAccessToDatabasesActionTest extends AbstractFeatureTest
{
    public function test_models_get_attached()
    {
        /** @var Server $server */
        $server = Server::factory()
            ->withProvider()
            ->create();

        $databases = Database::factory()
            ->for($server)
            ->count(2)
            ->create();

        $databaseUsers = DatabaseUser::factory()
            ->for($server)
            ->count(2)
            ->create();

        $this->actingAs($server->user);

        /** @var GrantDatabaseUsersAccessToDatabasesAction $action */
        $action = $this->app->make(GrantDatabaseUsersAccessToDatabasesAction::class);

        Bus::fake();
        Event::fake();

        $job = $action->execute($databaseUsers, $databases);

        $this->assertEquals(GrantDatabaseUsersAccessToDatabasesJob::class, get_class($job));

        $databases = Database::query()->findMany($databases->modelKeys());
        $databaseUsers = DatabaseUser::query()->findMany($databaseUsers->modelKeys());

        /** @var Database $database */
        foreach ($databases as $database) {
            $this->assertEquals(2, $database->databaseUsers->count());
            $this->assertEquals(1, $database->tasks);
            $this->assertTrue($database->hasTasks());
        }

        /** @var DatabaseUser $databaseUser */
        foreach ($databaseUsers as $databaseUser) {
            $this->assertEquals(2, $databaseUser->databases->count());
            $this->assertEquals(1, $databaseUser->tasks);
            $this->assertTrue($databaseUser->hasTasks());
        }

        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $databases->modelKeys()[0], 1);
        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $databases->modelKeys()[1], 1);

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[0], 1);
        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUsers->modelKeys()[1], 1);
    }
}
