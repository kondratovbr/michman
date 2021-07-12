<?php

namespace Tests\Feature\Databases;

use App\Events\Databases\DatabaseUpdatedEvent;
use App\Jobs\Servers\CreateDatabaseOnServerJob;
use App\Models\Database;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\CreateDatabaseScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateDatabaseOnServerJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Database $database */
        $database = Database::factory()
            ->withServer()
            ->create();

        Bus::fake();
        Event::fake();

        $job = new CreateDatabaseOnServerJob($database);

        $this->assertEquals('servers', $job->queue);

        $database->refresh();

        $this->assertEquals(1, $database->tasks);
        $this->assertTrue($database->hasTasks());

        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 1);

        $this->mock(CreateDatabaseScript::class, function (MockInterface $mock) use ($database) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Server $serverArg, string $dbNameArg) =>
                    $serverArg->is($database->server) && $dbNameArg === $database->name)
                ->once();
        });

        app()->call([$job, 'handle']);

        $database->refresh();

        $this->assertEquals(0, $database->tasks);
        $this->assertFalse($database->hasTasks());

        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 2);
    }
}
