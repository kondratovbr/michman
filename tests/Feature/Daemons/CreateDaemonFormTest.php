<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\StoreDaemonAction;
use App\DataTransferObjects\DaemonDto;
use App\Http\Livewire\Daemons\CreateDaemonForm;
use App\Models\Server;
use App\Models\User;
use App\Policies\DaemonPolicy;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateDaemonFormTest extends AbstractFeatureTest
{
    public function test_daemon_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        $data = [
            'command' => 'celery --help',
            'username' => 'michman',
            'directory' => '/home/michman',
            'processes' => 2,
            'start_seconds' => 10,
        ];

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('create')
                ->withArgs(function (
                    User    $userArg,
                    Server  $serverArg,
                ) use ($server) {
                    return $userArg->is($server->user)
                        && $serverArg->is($server);
                })
                ->twice()
                ->andReturnTrue();
        });

        $this->mock(StoreDaemonAction::class, function (MockInterface $mock) use ($server, $data) {
            $mock->shouldReceive('execute')
                ->withArgs(function (
                    DaemonDto $dataArg,
                    Server    $serverArg,
                ) use ($server, $data) {
                    return $dataArg->toArray() === $data
                        && $serverArg->is($server);
                })
                ->once();
        });

        Livewire::actingAs($server->user)->test(CreateDaemonForm::class, ['server' => $server])
            ->set('state', $data)
            ->call('store')
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertEmitted('daemon-stored')
            ->assertSet('state', [
                'command' => '',
                'username' => 'michman',
                'directory' => null,
                'processes' => 1,
                'start_seconds' => 1,
            ]);
    }
}
