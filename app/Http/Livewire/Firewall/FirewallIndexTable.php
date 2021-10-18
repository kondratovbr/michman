<?php declare(strict_types=1);

namespace App\Http\Livewire\Firewall;

use App\Actions\Firewall\DeleteFirewallRuleAction;
use App\Broadcasting\ServerChannel;
use App\Events\Firewall\FirewallRuleCreatedEvent;
use App\Events\Firewall\FirewallRuleDeletedEvent;
use App\Events\Firewall\FirewallRuleUpdatedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component as LivewireComponent;

class FirewallIndexTable extends LivewireComponent
{
    use AuthorizesRequests;
    use ListensForEchoes;

    public Server $server;

    public Collection $firewallRules;

    /** @var string[] */
    protected $listeners = [
        'firewall-rule-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServerChannel::name($this->server),
            [
                FirewallRuleCreatedEvent::class,
                FirewallRuleUpdatedEvent::class,
                FirewallRuleDeletedEvent::class,
            ],
            '$refresh',
        );
    }

    public function mount(): void
    {
        $this->authorize('index', [FirewallRule::class, $this->server]);
    }

    public function delete(DeleteFirewallRuleAction $deleteFirewallRule, string $ruleKey): void
    {
        $rule = FirewallRule::validated($ruleKey, $this->server->firewallRules);

        $this->authorize('delete', $rule);

        $deleteFirewallRule->execute($rule);
    }

    public function render(): View
    {
        $this->firewallRules = $this->server->firewallRules()->oldest()->get();

        return view('firewall.firewall-index-table');
    }
}
