<?php declare(strict_types=1);

namespace App\Http\Livewire\Pythons;

use App\Actions\Pythons\StorePythonAction;
use App\DataTransferObjects\PythonData;
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
    use AuthorizesRequests;

    public Server $server;

    public array $pythonVersions;
    public Collection $pythons;

    /** @var string[] */
    protected $listeners = [
        'python-stored' => '$refresh',
    ];

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
        $this->pythonVersions = Arr::keys(config('servers.python'));
        $this->pythons = $this->server->pythons;
    }

    /**
     * Install a new instance of Python on the server.
     */
    public function install(StorePythonAction $storePython, string $version): void
    {
        /*
         * TODO: CRITICAL! CONTINUE. Figure out a local setup of WebSockets with beyondcode/laravel-websockets and configure the app.
         *       Then, figure out Laravel Echo + Livewire. Or maybe just setup a free Pusher account first to try it all out.
         *       Then, implement the front-end part.
         */

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

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->authorize('index', [Python::class, $this->server]);

        return view('pythons.index-table');
    }
}
