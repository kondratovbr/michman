<?php declare(strict_types=1);

namespace App\Jobs\FirewallRules;

use App\Events\Firewall\FirewallRuleAddedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\FirewallRule;
use App\Scripts\Root\AddFirewallRuleScript;
use Illuminate\Support\Facades\DB;

class AddFirewallRuleToServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected FirewallRule $rule;

    public function __construct(FirewallRule $rule)
    {
        $this->setQueue('servers');

        $this->rule = $rule->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(AddFirewallRuleScript $addFirewallRule): void
    {
        DB::transaction(function () use ($addFirewallRule) {
            /** @var FirewallRule $rule */
            $rule = FirewallRule::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->rule->getKey());

            $addFirewallRule->execute(
                $rule->server,
                $rule->port,
                // We're going to "limit" the SSH port using the UFW built-in config as an additional brute-force protection for SSH.
                $rule->port == $rule->server->sshPort,
                $rule->fromIp,
            );

            $rule->status = FirewallRule::STATUS_ADDED;
            $rule->save();

            event(new FirewallRuleAddedEvent($rule));
        }, 5);
    }
}