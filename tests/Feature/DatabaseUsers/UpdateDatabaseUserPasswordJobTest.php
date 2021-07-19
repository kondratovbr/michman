<?php

namespace Tests\Feature\DatabaseUsers;

use App\Events\DatabaseUsers\DatabaseUserUpdatedEvent;
use App\Jobs\DatabaseUsers\UpdateDatabaseUserPasswordJob;
use App\Models\DatabaseUser;
use App\Models\Server;
use App\Scripts\Root\Mysql8_0\UpdateDatabaseUserPasswordScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class UpdateDatabaseUserPasswordJobTest extends AbstractFeatureTest
{
    public function test_job_parameters_and_logic()
    {
        /** @var DatabaseUser $databaseUser */
        $databaseUser = DatabaseUser::factory()->withServer()->create();

        $databaseUser->password = 'foobarbaz';
        $databaseUser->save();

        Bus::fake();
        Event::fake();

        $job = new UpdateDatabaseUserPasswordJob($databaseUser);

        $this->assertEquals('servers', $job->queue);

        $this->assertEquals(1, $databaseUser->tasks);

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey());

        $this->mock(UpdateDatabaseUserPasswordScript::class, function (MockInterface $mock) use ($databaseUser) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    Server $serverArg,
                    string $userNameArg,
                    string $passwordArg,
                ) use ($databaseUser) {
                    return $serverArg->is($databaseUser->server)
                        && $userNameArg === $databaseUser->name
                        && $passwordArg === 'foobarbaz';
                })
                ->once();
        });

        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('database_users', [
            'id' => $databaseUser->id,
            'password' => null,
        ]);

        $databaseUser->refresh();

        $this->assertNull($databaseUser->password);
        $this->assertEquals(0, $databaseUser->tasks);

        Event::assertDispatched(fn(DatabaseUserUpdatedEvent $event) => $event->databaseUserKey === $databaseUser->getKey(), 2);
    }
}
