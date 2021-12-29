<?php declare(strict_types=1);

namespace App\Http\Livewire\Pythons;

use App\Actions\Pythons\DeletePythonAction;
use App\Actions\Pythons\PatchPythonAction;
use App\Actions\Pythons\StorePythonAction;
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

// TODO: IMPORTANT! Cover deletion with tests as well.

class PythonsIndexTable extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

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

    public function mount(): void
    {
        $this->authorize('index', [Python::class, $this->server]);

        $this->pythonVersions = Arr::keys(config('servers.python'));
    }

    /** Install a new instance of Python on the server. */
    public function install(StorePythonAction $storePython, string $version): void
    {
        $version = Validator::make(
            ['version' => $version],
            ['version' => Rules::string(1, 8)
                ->in(Arr::keys(config('servers.python')))
                ->required()],
        )->validate()['version'];

        $this->authorize('create', [Python::class, $this->server, $version]);

        $storePython->execute($version, $this->server);
    }

    /** Update a Python installation on the server to the most recent patch version available. */
    public function patch(PatchPythonAction $patchPython, string $pythonKey): void
    {
        $python = Python::validated($pythonKey, $this->pythons);

        $this->authorize('update', $python);

        $patchPython->execute($python);
    }

    /** Remove a Python installation from the server. */
    public function remove(DeletePythonAction $deletePython, string $pythonKey): void
    {
        $python = Python::validated($pythonKey, $this->pythons);

        $this->authorize('delete', $python);

        $deletePython->execute($python);
    }

    public function render(): View
    {
        // TODO: Is there a caching opportunity here? So, no reloading these from the DB every time? See other similar Livewire tables as well.
        $this->pythons = $this->server->pythons()->get();

        return view('pythons.pythons-index-table');
    }
}
