<?php declare(strict_types=1);

namespace App\Http\Livewire\Databases;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class DatabasesIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $databases;
    public Collection $databaseUsers;

    protected function configureEchoListeners(): void
    {
        //
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        //
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $this->databases = $this->server->databases()->oldest()->get();
        $this->databaseUsers = $this->server->databaseUsers()->oldest()->get();

        return view('databases.index-table');
    }
}
