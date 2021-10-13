<?php

namespace Tests\Feature\Workers;

use App\Actions\Workers\DeleteWorkerAction;
use App\Actions\Workers\RestartWorkerAction;
use App\Actions\Workers\RetrieveWorkerLogAction;
use App\Actions\Workers\UpdateWorkersStatusesAction;
use App\Http\Livewire\Workers\WorkersIndexTable;
use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use App\Models\Worker;
use App\Policies\WorkerPolicy;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class WorkersIndexTableTest extends AbstractFeatureTest
{
    public function test_updating_statuses()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Collection $workers */
        $workers = Worker::factory()->for($project)->for($server)->count(3)->create();
        $user = $project->user;

        $this->mock(WorkerPolicy::class, function (MockInterface $mock) use ($project) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($project) {
                    return $userArg->is($project->user)
                        && $projectArg->is($project);
                })
                ->twice()
                ->andReturnTrue();
        });

        $this->mock(UpdateWorkersStatusesAction::class, function (MockInterface $mock) use ($project) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Project $projectArg) => $projectArg->is($project))
                ->once();
        });

        Livewire::actingAs($user)->test(WorkersIndexTable::class, ['project' => $project])
            ->call('updateStatuses')
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_restart_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Collection $workers */
        $workers = Worker::factory()->for($project)->for($server)->count(3)->create();
        $user = $project->user;
        /** @var Worker $worker */
        $worker = $workers->first();

        $this->mock(WorkerPolicy::class, function (MockInterface $mock) use ($user, $project, $worker) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($user, $project) {
                    return $userArg->is($user)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('restart')
                ->withArgs(function (
                    User $userArg,
                    Worker $workerArg,
                ) use ($user, $worker) {
                    return $userArg->is($user)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(RestartWorkerAction::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Worker $workerArg) => $workerArg->is($worker))
                ->once();
        });

        Livewire::actingAs($user)->test(WorkersIndexTable::class, ['project' => $project])
            ->call('restart', $worker->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_delete_action()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Collection $workers */
        $workers = Worker::factory()->for($project)->for($server)->count(3)->create();
        /** @var Worker $worker */
        $worker = $workers->first();
        $user = $project->user;

        $this->mock(WorkerPolicy::class, function (MockInterface $mock) use ($user, $project, $worker) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($user, $project) {
                    return $userArg->is($user)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('delete')
                ->withArgs(function (
                    User $userArg,
                    Worker $workerArg,
                ) use ($user, $worker) {
                    return $userArg->is($user)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(DeleteWorkerAction::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Worker $workerArg) => $workerArg->is($worker))
                ->once();
        });

        Livewire::actingAs($user)->test(WorkersIndexTable::class, ['project' => $project])
            ->call('delete', $worker->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors();
    }

    public function test_log_viewer()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Collection $workers */
        $workers = Worker::factory()->for($project)->for($server)->count(3)->create();
        /** @var Worker $worker */
        $worker = $workers->first();
        $user = $project->user;

        $this->mock(WorkerPolicy::class, function (MockInterface $mock) use ($user, $project, $worker) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($user, $project) {
                    return $userArg->is($user)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('view')
                ->withArgs(function (
                    User $userArg,
                    Worker $workerArg,
                ) use ($user, $worker) {
                    return $userArg->is($user)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(RetrieveWorkerLogAction::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Worker $workerArg) => $workerArg->is($worker))
                ->once()
                ->andReturn('Worker Log!');
        });

        Livewire::actingAs($user)->test(WorkersIndexTable::class, ['project' => $project])
            ->call('showLog', $worker->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('worker.id', $worker->id)
            ->assertSet('log', 'Worker Log!')
            ->assertSet('modalOpen', true)
            ->assertSet('error', false);
    }

    public function test_log_retrieving_failure_gets_handled()
    {
        /** @var Project $project */
        $project = Project::factory()->withUserAndServers()->create();
        /** @var Server $server */
        $server = $project->servers->first();
        /** @var Collection $workers */
        $workers = Worker::factory()->for($project)->for($server)->count(3)->create();
        /** @var Worker $worker */
        $worker = $workers->first();
        $user = $project->user;

        $this->mock(WorkerPolicy::class, function (MockInterface $mock) use ($user, $project, $worker) {
            $mock->shouldReceive('index')
                ->withArgs(function (
                    User $userArg,
                    Project $projectArg,
                ) use ($user, $project) {
                    return $userArg->is($user)
                        && $projectArg->is($project);
                })
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('view')
                ->withArgs(function (
                    User $userArg,
                    Worker $workerArg,
                ) use ($user, $worker) {
                    return $userArg->is($user)
                        && $workerArg->is($worker);
                })
                ->once()
                ->andReturnTrue();
        });

        $this->mock(RetrieveWorkerLogAction::class, function (MockInterface $mock) use ($worker) {
            $mock->shouldReceive('execute')
                ->withArgs(fn(Worker $workerArg) => $workerArg->is($worker))
                ->once()
                ->andReturnFalse();
        });

        Livewire::actingAs($user)->test(WorkersIndexTable::class, ['project' => $project])
            ->call('showLog', $worker->getKey())
            ->assertSuccessful()
            ->assertHasNoErrors()
            ->assertSet('worker.id', $worker->id)
            ->assertSet('log', null)
            ->assertSet('modalOpen', true)
            ->assertSet('error', true);
    }
}
