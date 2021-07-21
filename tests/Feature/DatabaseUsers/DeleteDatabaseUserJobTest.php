<?php

namespace Tests\Feature\DatabaseUsers;

use App\Events\DatabaseUsers\DatabaseUserDeletedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\DatabaseUsers\DeleteDatabaseUserJob;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\DeleteDatabaseUserScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDatabaseUserJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()
            ->withServer()
            ->create();

        Bus::fake();
        Event::fake();

        $job = new DeleteDatabaseUserJob($databaseUser);

        $this->assertEquals('servers', $job->queue);

        $databaseUser->refresh();

        $this->assertEquals(1, $databaseUser->tasks);
        $this->assertTrue($databaseUser->hasTasks());

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 1);

        $this->mock(DeleteDatabaseUserScript::class, function (MockInterface $mock) use ($databaseUser) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Server $serverArg, string $dbUserNameArg) =>
                    $serverArg->is($databaseUser->server) && $dbUserNameArg === $databaseUser->name)
                ->once();
        });

        app()->call([$job, 'handle']);

        $this->assertDatabaseMissing('database_users', [
            'id' => $databaseUser->id,
        ]);

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 2);
        Event::assertDispatched(fn(DatabaseUserDeletedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 1);
    }
}
