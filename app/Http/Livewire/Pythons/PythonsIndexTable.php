<?php declare(strict_types=1);

namespace App\Http\Livewire\Pythons;

use App\Models\Python;
use App\Models\Server;
use App\Support\Arr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

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

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->pythonVersions = Arr::keys(config('servers.python'));
        $this->pythons = $this->server->pythons;
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
