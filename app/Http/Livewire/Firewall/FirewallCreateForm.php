<?php declare(strict_types=1);

namespace App\Http\Livewire\Firewall;

use App\Actions\Firewall\StoreFirewallRuleAction;
use App\DataTransferObjects\FirewallRuleDto;
use App\Http\Livewire\Traits\TrimsInputBeforeValidation;
use App\Models\FirewallRule;
use App\Models\Server;
use App\Rules\PortRule;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class FirewallCreateForm extends LivewireComponent
{
    use AuthorizesRequests,
        TrimsInputBeforeValidation;

    public Server $server;

    public string|null $name = null;
    public string|null $port = null;
    public string|null $from_ip = null;

    public function rules(): array
    {
        return [
            'name' => Rules::string(1, 255)->required(),
            'port' => Rules::string(1, 11)->addRule(new PortRule)->required(),
            'from_ip' => Rules::ip()->nullable(),
        ];
    }

    /**
     * Store a new firewall rule.
     */
    public function store(StoreFirewallRuleAction $storeFirewallRule): void
    {
        $validated = $this->validate();

        $this->authorize('create', [FirewallRule::class, $this->server]);

        $storeFirewallRule->execute(FirewallRuleDto::fromArray($validated), $this->server);

        $this->reset(
            'name',
            'port',
            'from_ip',
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
