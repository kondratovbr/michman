<?php declare(strict_types=1);

namespace App\Http\Livewire\Pythons;

use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\PythonData;
use App\Events\Pythons\PythonInstalledEvent;
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
            PythonInstalledEvent::class,
            '$refresh',
        );
    }

    /**
     * Get the validation rules for the input data.
     */
    protected function rules(): array
    {
        return [
            'version' => Rules::string()
                ->in(Arr::keys(config('servers.python')))
                ->required(),
        ];
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
            $this->rules(),
        )->validate()['version'];

        $this->authorize('create', [Python::class, $this->server, $version]);

        $storePython->execute(new PythonData(
            server: $this->server,
            version: $version,
        ));
    }

    public function foobar(): void
    {
        dd('Foobar!');
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
