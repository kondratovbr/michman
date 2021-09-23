<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\StoreDaemonAction;
use App\DataTransferObjects\DaemonDto;
use App\Events\Daemons\DaemonCreatedEvent;
use App\Jobs\Daemons\StartDaemonJob;
use App\Models\Daemon;
use App\Models\Server;
use App\States\Daemons\Starting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFeatureTest;

class StoreDaemonActionTest extends AbstractFeatureTest
{
    public function test_daemon_gets_stored()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var StoreDaemonAction $action */
        $action = $this->app->make(StoreDaemonAction::class);

        Bus::fake();
        Event::fake();

        $data = [
            'command' => '/usr/bin/python3 --help',
            'username' => 'admin',
            'directory' => '/home/admin',
            'processes' => 1,
            'start_seconds' => 10,
        ];

        $action->execute(DaemonDto::fromArray($data), $server);

        $data['state'] = 'starting';

        $this->assertDatabaseHas('daemons', $data);

        $server->refresh();

        $this->assertCount(1, $server->daemons);

        /** @var Daemon $daemon */
        $daemon = $server->daemons->first();

        $this->assertEquals('/usr/bin/python3 --help', $daemon->command);
        $this->assertEquals('admin', $daemon->username);
        $this->assertEquals('/home/admin', $daemon->directory);
        $this->assertEquals(1, $daemon->processes);
        $this->assertEquals(10, $daemon->startSeconds);
        $this->assertTrue($daemon->state->is(Starting::class));

        $this->assertEquals('python3', $daemon->shortCommand);
        $this->assertEquals('admin', $daemon->shortDirectory);

        Bus::assertDispatched(StartDaemonJob::class);
        Event::assertDispatched(DaemonCreatedEvent::class);
    }

    public function test_daemon_with_null_directory_gets_stored()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        /** @var StoreDaemonAction $action */
        $action = $this->app->make(StoreDaemonAction::class);

        Bus::fake();
        Event::fake();

        $data = [
            'command' => '/usr/bin/python3 --help',
            'username' => 'admin',
            'directory' => null,
            'processes' => 1,
            'start_seconds' => 10,
        ];

        $action->execute(DaemonDto::fromArray($data), $server);

        $data['state'] = 'starting';

        $this->assertDatabaseHas('daemons', $data);

        $server->refresh();

        $this->assertCount(1, $server->daemons);

        /** @var Daemon $daemon */
        $daemon = $server->daemons->first();

        $this->assertNull($daemon->directory);

        Bus::assertDispatched(StartDaemonJob::class);
        Event::assertDispatched(DaemonCreatedEvent::class);
    }
}
