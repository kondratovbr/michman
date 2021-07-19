<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\UpdateDatabaseUserAction;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\DatabaseUsers\UpdateDatabaseUserPasswordJob;
use App\Jobs\Servers\GrantDatabaseUsersAccessToDatabasesJob;
use App\Jobs\Servers\RevokeDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\AbstractFeatureTest;

class UpdateDatabaseUserActionTest extends AbstractFeatureTest
{
    public function test_database_user_gets_updated()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory([
            'password' => Hash::make('foobarbaz'),
        ])->withServer()->create();
        $server = $databaseUser->server;
        $databases = Database::factory()->for($server)->count(2)->create();
        $databaseUser->databases()->sync($databases);
        $newDatabases = Database::factory()->for($server)->count(2)->create();

        /** @var UpdateDatabaseUserAction $action */
        $action = $this->app->make(UpdateDatabaseUserAction::class);

        Bus::fake();
        Event::fake();

        $returnedDatabaseUser = $action->execute(
            $databaseUser,
            'foobarbaz',
            $newDatabases,
        );

        $this->assertTrue($returnedDatabaseUser->is($databaseUser));
        $this->assertDatabaseHas('database_users', [
            'id' => $databaseUser->getKey(),
        ]);

        $databaseUser->refresh();

        $this->assertEquals(3, $databaseUser->tasks);
        $this->assertEquals('foobarbaz', $databaseUser->password);
        $this->assertEquals(2, $databaseUser->databases->count());
        $this->assertTrue($databaseUser->databases->modelKeys() == $newDatabases->modelKeys());
        $this->assertTrue($databaseUser->databases->diff($newDatabases)->isEmpty());

        /** @var Database $database */
        foreach ($databases as $database) {
            $database->refresh();
            $this->assertEquals(1, $database->tasks);
            Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey());
        }

        /** @var Database $database */
        foreach ($newDatabases as $database) {
            $database->refresh();
            $this->assertEquals(1, $database->tasks);
            Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey());
        }

        Bus::assertChained([
            UpdateDatabaseUserPasswordJob::class,
            RevokeDatabaseUsersAccessToDatabasesJob::class,
            GrantDatabaseUsersAccessToDatabasesJob::class,
        ]);

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 3);
    }
}
