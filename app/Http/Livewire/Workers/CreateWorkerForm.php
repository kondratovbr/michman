<?php declare(strict_types=1);

namespace App\Http\Livewire\Workers;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use App\Support\Arr;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! CONTINUE.

class CreateWorkerForm extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Project $project;

    public array $state = [
        'type' => 'celery',
        'serverId' => null,
        'processes' => 1,
    ];

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        //
    }

    public function mount(): void
    {
        $this->authorize('create', [Worker::class, $this->project]);

        $this->resetState();
    }

    protected function resetState(): void
    {
        $this->reset('state');

        // TODO: CRITICAL! Account for the fact that workers can only be run on "app", "web" or "worker" servers.
        $this->state['serverId'] = $this->project->servers()->firstOrFail()->getKey();
    }

    /**
     * Get an array of available worker types and their names for the type select field.
     */
    public function getTypesProperty(): array
    {
        $workers = Arr::keys(config('projects.workers'));

        return Arr::mapAssoc($workers, fn(int $index, string $type) => [$type, __("projects.queue.types.{$type}")]);
    }

    /**
     * Get an array of available servers types and their names for the server select field.
     */
    public function getServersProperty(): array
    {
        return $this->project->servers->mapWithKeys(fn(Server $server) => [$server->getKey() => $server->name])->toArray();
    }

    public function render(): View
    {
        return view('workers.create-worker-form');
    }
}
