<?php

namespace Tests\Feature\Databases;

use App\Actions\Databases\DeleteDatabaseAction;
use App\Actions\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesAction;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\Databases\DeleteDatabaseJob;
use App\Jobs\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDatabaseActionTest extends AbstractFeatureTest
{
    public function test_jobs_get_chained()
    {
        /** @var Database $database */
        $database = Database::factory()->withServer()->withDatabaseUsers(2)->create();

        Bus::fake();
        Event::fake();

        $this->mockBind(RevokeDatabaseUsersAccessToDatabasesAction::class, function (MockInterface $mock) use ($database) {
            $mock
                ->shouldReceive('execute')
                ->withArgs(function (
                    Collection $databaseUsersArg,
                    Collection $databasesArg,
                ) use ($database) {
                    return $databaseUsersArg->count() == 2
                        && $databasesArg->count() == 1
                        && $databasesArg->first()->is($database);
                })
                ->once()
                ->andReturn(new RevokeDatabaseUsersAccessToDatabasesJob(
                    $database->databaseUsers,
                    collection([$database]),
                ));
        });

        /** @var DeleteDatabaseAction $action */
        $action = $this->app->make(DeleteDatabaseAction::class);

        $action->execute($database);

        Bus::assertChained([
            RevokeDatabaseUsersAccessToDatabasesJob::class,
            DeleteDatabaseJob::class,
        ]);

        $database->refresh();

        $this->assertEquals(2, $database->tasks);
        $this->assertEquals(1, $database->databaseUsers[0]->tasks);
        $this->assertEquals(1, $database->databaseUsers[1]->tasks);

        Event::assertDispatched(DatabaseUpdatedEvent::class);
        Event::assertDispatchedTimes(DatabaseUserUpdatedEvent::class, 2);
    }
}
