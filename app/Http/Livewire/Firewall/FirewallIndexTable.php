<?php declare(strict_types=1);

namespace App\Http\Livewire\Firewall;

use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

class FirewallIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $firewallRules;

    protected function configureEchoListeners(): void
    {
        //
    }

    public function mount(): void
    {
        $this->authorize('index', [FirewallRule::class, $this->server]);
    }

    public function render(): View
    {
        $this->firewallRules = $this->server->firewallRules()->orderBy('port')->get();

        return view('firewall.index-table');
    }
}
