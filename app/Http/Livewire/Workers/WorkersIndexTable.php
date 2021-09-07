<?php declare(strict_types=1);

namespace App\Http\Livewire\Workers;

use App\Actions\Workers\RestartWorkerAction;
use App\Broadcasting\ProjectChannel;
use App\Events\Workers\WorkerCreatedEvent;
use App\Events\Workers\WorkerDeletedEvent;
use App\Events\Workers\WorkerUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! This thing needs a log viewer and an "Check Workers Status" button.
 */

// TODO: CRITICAL! Cover with tests.

class WorkersIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Project $project;

    public Collection $workers;

    /** @var string[] */
    protected $listeners = [
        'worker-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ProjectChannel::name($this->project),
            [
                WorkerCreatedEvent::class,
                WorkerUpdatedEvent::class,
                WorkerDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [Worker::class, $this->project]);
    }

    /**
     * Restart a queue worker.
     */
    public function restart(string $workerKey, RestartWorkerAction $action): void
    {
        $worker = Worker::validated($workerKey, $this->workers);

        $this->authorize('restart', $worker);

        $action->execute($worker);
    }

    /**
     * Stop and delete a queue worker.
     */
    public function delete(string $workerKey): void
    {
        $worker = Worker::validated($workerKey, $this->workers);

        $this->authorize('delete', $worker);

        //
    }

    public function render(): View
    {
        $this->workers = $this->project->workers()->oldest()->get();

        return view('workers.workers-index-table');
    }
}
