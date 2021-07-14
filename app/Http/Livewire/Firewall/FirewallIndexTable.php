<?php declare(strict_types=1);

namespace App\Http\Livewire\Firewall;

use App\Actions\Firewall\DeleteFirewallRuleAction;
use App\Broadcasting\ServersChannel;
use App\Events\Firewall\FirewallRuleAddedEvent;
use App\Events\Firewall\FirewallRuleDeletedEvent;
use App\Http\Livewire\Traits\ListensForEchoes;
use App\Models\FirewallRule;
use App\Models\Server;
use App\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Livewire\Component as LivewireComponent;

class FirewallIndexTable extends LivewireComponent
{
    use AuthorizesRequests,
        ListensForEchoes;

    public Server $server;

    public Collection $firewallRules;

    /** @var string[] */
    protected $listeners = [
        'firewall-rule-stored' => '$refresh',
    ];

    protected function configureEchoListeners(): void
    {
        $this->echoPrivate(
            ServersChannel::name($this->server),
            [
                FirewallRuleAddedEvent::class,
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
        $ruleKey = Validator::make(
            ['rule_key' => $ruleKey],
            ['rule_key' => Rules::string(1, 16)
                ->in($this->firewallRules->pluck('id')->toArray())
                ->required()],
        )->validate()['rule_key'];

        /** @var FirewallRule $rule */
        $rule = $this->server->firewallRules()->findOrFail($ruleKey);

        $this->authorize('delete', $rule);

        $deleteFirewallRule->execute($rule);
    }

    public function render(): View
    {
        $this->firewallRules = $this->server->firewallRules()->oldest()->get();

        return view('firewall.index-table');
    }
}
