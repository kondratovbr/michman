<?php

namespace Tests\Feature\DatabaseUsers;

use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\Servers\CreateDatabaseUserOnServerJob;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\CreateDatabaseUserScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateDatabaseUserOnServerJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory([
            'password' => 'password',
        ])
            ->withServer()
            ->create();

        Bus::fake();
        Event::fake();

        $job = new CreateDatabaseUserOnServerJob($databaseUser);

        $this->assertEquals('servers', $job->queue);

        $databaseUser->refresh();

        $this->assertEquals(1, $databaseUser->tasks);
        $this->assertTrue($databaseUser->hasTasks());

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 1);

        $this->mock(CreateDatabaseUserScript::class, function (MockInterface $mock) use ($databaseUser) {
            $mock->shouldReceive('execute')
                ->withArgs(
                    fn(Server $serverArg, string $dbUserNameArg, string $passwordArg) =>
                        $serverArg->is($databaseUser->server)
                        && $dbUserNameArg === $databaseUser->name
                        && $passwordArg === $databaseUser->password
                )
                ->once();
        });

        app()->call([$job, 'handle']);

        $databaseUser->refresh();

        $this->assertNull($databaseUser->password);

        $this->assertEquals(0, $databaseUser->tasks);
        $this->assertFalse($databaseUser->hasTasks());

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 2);
    }
}
