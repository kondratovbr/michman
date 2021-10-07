<?php declare(strict_types=1);

namespace App\Http\Livewire\Servers;

use App\Broadcasting\ServerChannel;
use App\Broadcasting\UserChannel;
use App\Events\Servers\ServerCreatedEvent;
use App\Events\Servers\ServerDeletedEvent;
use App\Events\Servers\ServerUpdatedEvent;
use App\Facades\Auth;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class ServersIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Collection $servers;

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            UserChannel::name(Auth::user()),
            [
                ServerCreatedEvent::class,
                ServerUpdatedEvent::class,
                ServerDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [Server::class, Auth::user()]);
    }

    public function render(): View
    {
        $this->servers = Auth::user()->servers()
            ->with('pythons')
            ->oldest()
            ->get();

        return view('servers.servers-index-table');
    }
}
