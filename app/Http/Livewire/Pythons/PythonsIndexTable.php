<?php declare(strict_types=1);

namespace App\Http\Livewire\Pythons;

use App\Actions\Pythons\PatchPythonAction;
use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\PythonData;
use App\Events\Pythons\PythonInstalledEvent;
use App\Events\Pythons\PythonPatchedEvent;
use App\Events\Pythons\PythonRemovedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Python;
use App\Models\Server;
use App\Support\Arr;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;
use Illuminate\Support\Facades\Validator;

class PythonsIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public array $pythonVersions;
    public Collection $pythons;

    protected $listeners = [
        'python-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            'servers.' . $this->server->getKey(),
            [
                PythonInstalledEvent::class,
                PythonPatchedEvent::class,
                PythonRemovedEvent::class,
            ],
            '$refresh',
        );
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->authorize('index', [Python::class, $this->server]);

        $this->pythonVersions = Arr::keys(config('servers.python'));
    }

    /**
     * Install a new instance of Python on the server.
     */
    public function install(StorePythonAction $storePython, string $version): void
    {
        $version = Validator::make(
            ['version' => $version],
            ['version' => Rules::string(1, 8)
                ->in(Arr::keys(config('servers.python')))
                ->required()],
        )->validate()['version'];

        $this->authorize('create', [Python::class, $this->server, $version]);

        $storePython->execute(new PythonData(
            server: $this->server,
            version: $version,
        ));
    }

    /**
     * Update a Python installation on the server to the most recent patch version available.
     */
    public function patch(PatchPythonAction $patchPython, string $pythonKey): void
    {
        $pythonKey = Validator::make(
            ['python_key' => $pythonKey],
            ['python_key' => Rules::string(1, 16)
                ->in($this->pythons->pluck('id')->toArray())
                ->required()],
        )->validate()['python_key'];

        /** @var Python $python */
        $python = $this->server->pythons()->findOrFail($pythonKey);

        $this->authorize('update', $python);

        $patchPython->execute($python);
    }

    /**
     * Remove a Python installation from the server.
     */
    public function remove(string $pythonKey): void
    {
        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        // TODO: Is there are caching opportunity here? So, no reloading these from the DB every time?
        $this->pythons = $this->server->pythons()->get();

        return view('pythons.index-table');
    }
}
