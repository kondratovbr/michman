<?php declare(strict_types=1);

namespace App\Http\Livewire\Workers;

use App\Actions\Workers\StoreWorkerAction;
use App\DataTransferObjects\WorkerDto;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\Project;
use App\Models\Server;
use App\Models\Worker;
use App\Support\Arr;
use App\Support\Str;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

/*
 * TODO: CRITICAL! Need to make sure the app actually has Celery included. The user has to do this themselves since Celery requires at least some configuration. So, just don't allow to create workers if Celery isn't installed for this application. Or maybe offer to install it (later?)?
 */

/**
 * @property-read string[] $types
 * @property-read string[] $servers
 */
class CreateWorkerForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation,
        ListensForEchoes;

    public Project $project;

    public array $state = [
        'type' => 'celery',
        'serverId' => null,
        'app' => null,
        'processes' => null,
        'queues' => null,
        'stop_seconds' => 600,
        'max_tasks_per_child' => null,
        'max_memory_per_child' => null,
    ];

    /** @var string[] */
    protected $listeners = [
        //
    ];

    protected function configureEchoListeners(): void
    {
        //
    }

    protected function prepareForValidation($attributes): array
    {
        $state = $attributes['state'];

        if (is_string($state['queues'])) {
            $state['queues'] = Arr::map(
                explode(',', Str::lower($state['queues'])),
                fn(string $domain) => trim($domain)
            );
        }

        $attributes['state'] = $state;

        return $attributes;
    }

    public function rules(): array
    {
        return Arr::mapAssoc([
            'type' => Rules::string(1, 255)->in(Arr::keys($this->types))->required(),
            'serverId' => Rules::integer()->in(Arr::keys($this->servers))->required(),
            'app' => Rules::string(1, 255)->nullable(),
            'processes' => Rules::integer(1)->nullable(),
            'queues' => Rules::array()->nullable(),
            'queues.*' => Rules::alphaNumDashString(1, 255),
            'stop_seconds' => Rules::integer(0)->nullable(),
            'max_tasks_per_child' => Rules::integer(1)->nullable(),
            'max_memory_per_child' => Rules::integer(1)->nullable(),
        ], fn(string $name, $rules) => ["state.$name", $rules]);
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

        $this->state['app'] = "{$this->project->package}.celery";
    }

    /** Get an array of available worker types and their names for the type select field. */
    public function getTypesProperty(): array
    {
        $workers = Arr::keys(config('projects.workers'));

        return Arr::mapAssoc($workers, fn(int $index, string $type) => [$type, __("projects.queue.types.{$type}")]);
    }

    /** Get an array of available servers types and their names for the server select field. */
    public function getServersProperty(): array
    {
        return $this->project->servers->mapWithKeys(fn(Server $server) => [$server->getKey() => $server->name])->toArray();
    }

    /** Store a newly configured queue worker. */
    public function store(StoreWorkerAction $action): void
    {
        $state = $this->validate()['state'];

        $this->authorize('create', [Worker::class, $this->project]);

        $action->execute(new WorkerDto(
            type: $state['type'],
            app: $state['app'],
            processes: $state['processes'],
            queues: $state['queues'],
            stop_seconds: $state['stop_seconds'],
            max_tasks_per_child: $state['max_tasks_per_child'],
            max_memory_per_child: is_null($state['max_memory_per_child']) ? null : ($state['max_memory_per_child'] * 1024), // We're asking users for MiB for convenience, but Celery expects KiB.
        ), $this->project, $this->project->servers()->findOrFail($state['serverId']));

        $this->resetState();

        $this->emit('worker-stored');
    }

    public function render(): View
    {
        return view('workers.create-worker-form');
    }
}
