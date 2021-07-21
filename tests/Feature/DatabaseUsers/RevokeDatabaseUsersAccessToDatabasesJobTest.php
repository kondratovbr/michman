<?php

namespace Tests\Feature\DatabaseUsers;

use App\Events\Databases\DatabaseUpdatedEvent;
use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\DatabaseUsers\RevokeDatabaseUsersAccessToDatabasesJob;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\WorkerSshKey;
use App\Scripts\Root\Mysql8_0\RevokeDatabaseUserAccessToDatabaseScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;
use Tests\Feature\Traits\MocksSshSessions;

class RevokeDatabaseUsersAccessToDatabasesJobTest extends AbstractFeatureTest
{
    use MocksSshSessions;

    public function test_job_parameters_and_logic()
    {
        /** @var Server $server */
        $server = (WorkerSshKey::factory()
            ->withServer()
            ->create())->server;

        $databases = Database::factory()
            ->for($server)
            ->count(2)
            ->create();

        $databaseUsers = DatabaseUser::factory()
            ->for($server)
            ->count(2)
            ->create();

        Bus::fake();
        Event::fake();

        $job = new RevokeDatabaseUsersAccessToDatabasesJob($databaseUsers, $databases);

        $this->assertEquals('servers', $job->queue);

        $databases = Database::query()->findMany($databases->modelKeys());
        $databaseUsers = DatabaseUser::query()->findMany($databaseUsers->modelKeys());

        /** @var Database $database */
        foreach ($databases as $database) {
            $this->assertEquals(1, $database->tasks);
            $this->assertTrue($database->hasTasks());
            Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 1);
        }

        /** @var DatabaseUser $databaseUser */
        foreach ($databaseUsers as $databaseUser) {
            $this->assertEquals(1, $databaseUser->tasks);
            $this->assertTrue($databaseUser->hasTasks());
            Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 1);
        }

        $this->mockSftp();

        $this->mock(
            RevokeDatabaseUserAccessToDatabaseScript::class,
            function (MockInterface $mock) use ($server, $databases, $databaseUsers) {
                $mock->shouldReceive('execute')
                    ->withArgs(function (Server $serverArg, string $dbNameArg, string $userNameArg, $ssh) use ($server, $databases, $databaseUsers) {
                        return $serverArg->is($server)
                            && in_array($dbNameArg, $databases->pluck('name')->toArray())
                            && in_array($userNameArg, $databaseUsers->pluck('name')->toArray())
                            && $ssh instanceof SFTP;
                    })
                    ->times(4);
            }
        );

        app()->call([$job, 'handle']);

        $databases = Database::query()->findMany($databases->modelKeys());
        $databaseUsers = DatabaseUser::query()->findMany($databaseUsers->modelKeys());

        /** @var Database $database */
        foreach ($databases as $database) {
            $this->assertEquals(0, $database->tasks);
            $this->assertFalse($database->hasTasks());
            Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 2);
        }

        /** @var DatabaseUser $databaseUser */
        foreach ($databaseUsers as $databaseUser) {
            $this->assertEquals(0, $databaseUser->tasks);
            $this->assertFalse($databaseUser->hasTasks());
            Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 2);
        }
    }
}
