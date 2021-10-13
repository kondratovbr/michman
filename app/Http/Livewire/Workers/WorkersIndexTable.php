<?php declare(strict_types=1);

namespace App\Http\Livewire\Workers;

use App\Actions\Workers\DeleteWorkerAction;
use App\Actions\Workers\RestartWorkerAction;
use App\Actions\Workers\RetrieveWorkerLogAction;
use App\Actions\Workers\UpdateWorkersStatusesAction;
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

class WorkersIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Project $project;

    public Collection $workers;

    /** Indicates if a log viewer modal should currently be open. */
    public bool $modalOpen = false;
    /** A worker for which logs are currently shown. */
    public Worker|null $worker = null;
    /** Currently shown worker logs. */
    public string|null $log = null;
    /** Indicates if we failed to retrieve logs. */
    public bool $error = false;

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

    /** Update the worker statuses. */
    public function updateStatuses(UpdateWorkersStatusesAction $action): void
    {
        $this->authorize('index', [Worker::class, $this->project]);

        $action->execute($this->project);
    }

    /** Open a modal with a worker output log. */
    public function showLog(string $workerKey, RetrieveWorkerLogAction $action): void
    {
        $worker = Worker::validated($workerKey, $this->workers);

        $this->authorize('view', $worker);

        $this->worker = $worker;

        $result = $action->execute($worker);

        if ($result === false) {
            $this->error = true;
            $this->log = null;
        } else {
            $this->log = $result;
        }

        $this->modalOpen = true;
    }

    /** Reset data when the modal is closed. */
    public function updatedModalOpen(bool $value): void
    {
        if ($value) return;

        $this->worker = null;
        $this->log = null;
        $this->error = false;
    }

    /** Restart a queue worker. */
    public function restart(string $workerKey, RestartWorkerAction $action): void
    {
        $worker = Worker::validated($workerKey, $this->workers);

        $this->authorize('restart', $worker);

        $action->execute($worker);
    }

    /** Stop and delete a queue worker. */
    public function delete(string $workerKey, DeleteWorkerAction $action): void
    {
        $worker = Worker::validated($workerKey, $this->workers);

        $this->authorize('delete', $worker);

        $action->execute($worker);
    }

    public function render(): View
    {
        $this->workers = $this->project->workers()->oldest()->get();

        return view('workers.workers-index-table');
    }
}
