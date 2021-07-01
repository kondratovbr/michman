<?php declare(strict_types=1);

namespace App\Http\Livewire\Firewall;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleData;
use App\Http\Livewire\Traits\TrimsInput;
use App\Models\FirewallRule;
use App\Models\Server;
use App\Rules\PortRule;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

// TODO: CRITICAL! Cover with tests.

class FirewallCreateForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInput;

    public Server $server;

    public string|null $name = null;
    public string|null $port = null;
    public string|null $fromIp = null;

    public function rules(): array
    {
        return [
            'name' => Rules::string(1, 255)->required(),
            'port' => Rules::string(1, 11)->addRule(new PortRule)->required(),
            'fromIp' => Rules::ip()->nullable(),
        ];
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        //
    }

    /**
     * Store a new firewall rule.
     */
    public function store(StoreFirewallRuleAction $storeFirewallRule): void
    {
        $validated = $this->validate();

        $this->authorize('create', [FirewallRule::class, $this->server]);

        $storeFirewallRule->execute(new FirewallRuleData(
            name: $validated['name'],
            port: $validated['port'],
            from_ip: $validated['fromIp'],
        ), $this->server);

        $this->reset(
            'name',
            'port',
            'fromIp',
        );

        // This event is used to show the success message.
        // TODO: CRITICAL! Implement this.
        $this->emit('saved');
        // This event is used to update the providers table.
        $this->emit('firewall-rule-stored');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('firewall.create-form');
    }
}
