<?php

namespace Tests\Feature\Daemons;

use App\Actions\Daemons\DeleteDaemonAction;
use App\Actions\Daemons\RestartDaemonAction;
use App\Actions\Daemons\RetrieveDaemonLogAction;
use App\Actions\Daemons\StartDaemonAction;
use App\Actions\Daemons\StopDaemonAction;
use App\Actions\Daemons\UpdateDaemonsStatusesAction;
use App\Http\Livewire\Daemons\DaemonsIndexTable;
use App\Models\Daemon;
use App\Models\Server;
use App\Models\User;
use App\Policies\DaemonPolicy;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DaemonsIndexTableTest extends AbstractFeatureTest
{
    public function test_updating_statuses()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->twice()
                ->andReturnTrue();
        });

        $this->mock(UpdateDaemonsStatusesAction::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Server $serverArg) => $serverArg->is($server))
                ->once();
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('updateStatuses')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_stop_action()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Daemon $daemon */
        $daemon = $daemons[1];
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user, $daemon) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('update')
                ->withArgs(function (
                    User $userArg,
                    Daemon $daemonArg,
                ) use ($user, $daemon) {
                    return $userArg->is($user)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(StopDaemonAction::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Daemon $daemonArg) => $daemonArg->is($daemon))
                ->once();
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('stop', $daemon->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_start_action()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Daemon $daemon */
        $daemon = $daemons[1];
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user, $daemon) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('update')
                ->withArgs(function (
                    User $userArg,
                    Daemon $daemonArg,
                ) use ($user, $daemon) {
                    return $userArg->is($user)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(StartDaemonAction::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Daemon $daemonArg) => $daemonArg->is($daemon))
                ->once();
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('start', $daemon->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_restart_action()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Daemon $daemon */
        $daemon = $daemons[1];
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user, $daemon) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('update')
                ->withArgs(function (
                    User $userArg,
                    Daemon $daemonArg,
                ) use ($user, $daemon) {
                    return $userArg->is($user)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(RestartDaemonAction::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Daemon $daemonArg) => $daemonArg->is($daemon))
                ->once();
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('restart', $daemon->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_delete_action()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Daemon $daemon */
        $daemon = $daemons[1];
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user, $daemon) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('delete')
                ->withArgs(function (
                    User $userArg,
                    Daemon $daemonArg,
                ) use ($user, $daemon) {
                    return $userArg->is($user)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(DeleteDaemonAction::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Daemon $daemonArg) => $daemonArg->is($daemon))
                ->once();
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('delete', $daemon->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_log_viewer()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Daemon $daemon */
        $daemon = $daemons[1];
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user, $daemon) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('view')
                ->withArgs(function (
                    User $userArg,
                    Daemon $daemonArg,
                ) use ($user, $daemon) {
                    return $userArg->is($user)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(RetrieveDaemonLogAction::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Daemon $daemonArg) => $daemonArg->is($daemon))
                ->once()
                ->andReturn('Daemon logs!');
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('showLog', $daemon->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('daemon.id', $daemon->id)
            ->assertSet('log', 'Daemon logs!')
            ->assertSet('modalOpen', true)
            ->assertSet('error', false);
    }

    public function test_log_retrieving_failure_gets_handled()
    {
        /** @var Collection $daemons */
        $daemons = Daemon::factory()->withServer()->count(3)->create();
        /** @var Daemon $daemon */
        $daemon = $daemons[1];
        /** @var Server $server */
        $server = $daemons->first()->server;
        $user = $server->user;

        $this->mock(DaemonPolicy::class, function (MockInterface $mock) use ($server, $user, $daemon) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Server $serverArg,
                ) use ($server, $user) {
                    return $userArg->is($user)
                        && $serverArg->is($server);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('view')
                ->withArgs(function (
                    User $userArg,
                    Daemon $daemonArg,
                ) use ($user, $daemon) {
                    return $userArg->is($user)
                        && $daemonArg->is($daemon);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(RetrieveDaemonLogAction::class, function (MockInterface $mock) use ($daemon) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Daemon $daemonArg) => $daemonArg->is($daemon))
                ->once()
                ->andReturnFalse();
        });

        Livewire::actingAs($user)->test(DaemonsIndexTable::class, ['server' => $server])
            ->call('showLog', $daemon->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('daemon.id', $daemon->id)
            ->assertSet('log', null)
            ->assertSet('modalOpen', true)
            ->assertSet('error', true);
    }
}
