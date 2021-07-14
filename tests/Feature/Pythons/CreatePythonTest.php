<?php

namespace Tests\Feature\Pythons;

use App\Actions\Pythons\PatchPythonAction;
use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\PythonData;
use App\Http\Livewire\Pythons\PythonsIndexTable;
use App\Models\Python;
use App\Models\Server;
use App\Models\User;
use App\Policies\PythonPolicy;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreatePythonTest extends AbstractFeatureTest
{
    public function test_python_can_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        $this->actingAs($server->user);

        $this->mock(PythonPolicy::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($server->user)
                    && $serverArg->is($server)
                )
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('create')
                ->withArgs(fn(User $userArg, Server $serverArg, string $versionArg) =>
                    $userArg->is($server->user)
                    && $serverArg->is($server)
                    && $versionArg === '3_9'
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(PythonsIndexTable::class, ['server' => $server])
            ->call('install',
                Mockery::mock(StorePythonAction::class,
                    function (MockInterface $mock) use ($server) {
                        $mock->shouldReceive('execute')
                            ->withArgs(function (PythonData $dataArg, Server $serverArg) use ($server) {
                                return $dataArg->version === '3_9'
                                    && $serverArg->is($server);
                            })
                            ->once()
                            ->andReturn(new Python);
                    },
                ),
                '3_9'
            )
            ->assertOk()
            ->assertHasNoErrors();
    }

    public function test_python_with_unsupported_version_cannot_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        $this->actingAs($server->user);

        $this->mock(PythonPolicy::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($server->user)
                    && $serverArg->is($server)
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(PythonsIndexTable::class, ['server' => $server])
            ->call('install',
                Mockery::mock(StorePythonAction::class,
                    function (MockInterface $mock) {
                        $mock->shouldNotHaveBeenCalled();
                    },
                ),
                '2_1'
            )
            ->assertHasErrors('version');
    }

    public function test_python_with_empty_version_cannot_be_created()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();

        $this->actingAs($server->user);

        $this->mock(PythonPolicy::class, function (MockInterface $mock) use ($server) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($server->user)
                    && $serverArg->is($server)
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(PythonsIndexTable::class, ['server' => $server])
            ->call('install',
                Mockery::mock(StorePythonAction::class,
                    function (MockInterface $mock) {
                        $mock->shouldNotHaveBeenCalled();
                    },
                ),
                ''
            )
            ->assertHasErrors('version');
    }

    public function test_python_can_be_patched()
    {
        /** @var Server $server */
        $server = Server::factory()->withProvider()->create();
        /** @var Python $python */
        $python = Python::factory()->for($server)->create();

        $this->actingAs($server->user);

        $this->mock(PythonPolicy::class, function (MockInterface $mock) use ($server, $python) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($server->user)
                    && $serverArg->is($server)
                )
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('update')
                ->withArgs(fn(User $userArg, Python $pythonArg) =>
                    $userArg->is($server->user)
                    && $pythonArg->is($python)
                )
                ->once()
                ->andReturnTrue();
        });

        Livewire::test(PythonsIndexTable::class, ['server' => $server])
            ->call('patch',
                Mockery::mock(PatchPythonAction::class,
                    function (MockInterface $mock) use ($python) {
                        $mock->shouldReceive('execute')
                            ->withArgs(fn(Python $pythonArg) => $pythonArg->is($python))
                            ->once()
                            ->andReturn(new Python);
                    },
                ),
                (string) $python->getKey()
            )
            ->assertOk()
            ->assertHasNoErrors();
    }
}
