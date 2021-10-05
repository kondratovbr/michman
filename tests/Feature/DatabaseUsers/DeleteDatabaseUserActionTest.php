<?php

namespace Tests\Feature\DatabaseUsers;

use App\Actions\DatabaseUsers\DeleteDatabaseUserAction;
use App\Actions\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesAction;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\DatabaseUsers\DeleteDatabaseUserJob;
use App\Jobs\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use Illuminate\Support\Collection as BaseCollection;
use App\Collections\EloquentCollection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDatabaseUserActionTest extends AbstractFeatureTest
{
    public function test_database_user_gets_deleted()
    {
        /** @var DatabaseUser $dbUser */
        $dbUser = DatabaseUser::factory()->withServer()->create();
        /** @var EloquentCollection $databases */
        $databases = Database::factory()->for($dbUser->server)->count(3)->create();
        $dbUser->databases()->sync($databases);

        Bus::fake();
        Event::fake();

        $this->mock(RevokeDatabaseUsersAccessToDatabasesAction::class, function (MockInterface $mock) use ($dbUser, $databases) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    BaseCollection $dbUsersArg,
                    BaseCollection $databasesArg,
                ) use ($dbUser, $databases) {
                    return $dbUsersArg->count() === 1
                        && $databasesArg->count() === 3
                        && $dbUsersArg->first()->is($dbUser);
                })
                ->once()
                ->andReturn(new RevokeDatabaseUsersAccessToDatabasesJob(
                    collection([$dbUser]), $databases
                ));
        });

        /** @var DeleteDatabaseUserAction $action */
        $action = $this->app->make(DeleteDatabaseUserAction::class);

        $action->execute($dbUser);

        Bus::assertChained([
            RevokeDatabaseUsersAccessToDatabasesJob::class,
            DeleteDatabaseUserJob::class,
        ]);

        $dbUser->refresh();
        $databases->refreshAll();

        $this->assertEquals(2, $dbUser->tasks);
        $this->assertEquals(1, $databases[0]->tasks);
        $this->assertEquals(1, $databases[1]->tasks);
        $this->assertEquals(1, $databases[2]->tasks);

        Event::assertDispatched(DatabaseUserUpdatedEvent::class);
        Event::assertDispatchedTimes(DatabaseUpdatedEvent::class, 3);
    }
}
