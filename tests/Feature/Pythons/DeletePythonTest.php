<?php

namespace Tests\Feature\Pythons;

use App\Models\Python;
use App\Models\Server;
use App\Models\User;
use App\Policies\PythonPolicy;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class DeletePythonTest extends AbstractFeatureTest
{
    public function test_python_can_be_deleted()
    {
        $this->markTestSkipped('Python deletion is not implemented yet.');
        return;

        /** @var Python $python */
        $python = Python::factory()->withServer()->create();

        $this->actingAs($python->user);

        $this->mock(PythonPolicy::class, function (MockInterface $mock) use ($python) {
            $mock->shouldReceive('index')
                ->withArgs(fn(User $userArg, Server $serverArg) =>
                    $userArg->is($python->user)
                    && $serverArg->is($python->server)
                )
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('delete')
                ->withArgs(fn(User $userArg, Python $pythonArg) =>
                    $userArg->is($python->user)
                    && $pythonArg->is($python)
                )
                ->once()
                ->andReturnTrue();
        });

        // Livewire::test('remove')
    }
}
