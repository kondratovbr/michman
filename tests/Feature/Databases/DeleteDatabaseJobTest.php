<?php

namespace Tests\Feature\Databases;

use App\Events\Databases\DatabaseDeletedEvent;
use App\Events\Databases\DatabaseUpdatedEvent;
use App\Jobs\Servers\DeleteDatabaseJob;
use App\Models\Database;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\DeleteDatabaseScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeleteDatabaseJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var Database $database */
        $database = Database::factory()
            ->withServer()
            ->create();

        Bus::fake();
        Event::fake();

        $job = new DeleteDatabaseJob($database);

        $this->assertEquals('servers', $job->queue);

        $database->refresh();

        $this->assertEquals(1, $database->tasks);
        $this->assertTrue($database->hasTasks());

        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 1);

        $this->mock(DeleteDatabaseScript::class, function (MockInterface $mock) use ($database) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Server $serverArg, string $dbNameArg) => $serverArg->is($database->server) && $dbNameArg === $database->name)
                ->once();
        });

        app()->call([$job, 'handle']);

        $this->assertDatabaseMissing('databases', [
            'id' => $database->id,
        ]);

        Event::assertDispatched(fn(DatabaseUpdatedEvent $event) => $event->databaseKey === $database->getKey(), 2);
        Event::assertDispatched(fn(DatabaseDeletedEvent $event) => $event->databaseKey === $database->getKey(), 1);
    }
}
