<?php declare(strict_types=1);

namespace App\Jobs\FirewallRules;

use App\Events\Firewall\FirewallRuleDeletedEvent;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\FirewallRule;
use App\Scripts\Root\DeleteFirewallRuleScript;
use Illuminate\Support\Facades\DB;

class DeleteFirewallRuleJob extends AbstractJob
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
    public function handle(DeleteFirewallRuleScript $deleteFirewallRule): void
    {
        DB::transaction(function () use ($deleteFirewallRule) {
            /** @var FirewallRule $rule */
            $rule = FirewallRule::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($this->rule->getKey());

            $server = $rule->server;

            $deleteFirewallRule->execute(
                $server,
                $rule->port,
                $rule->port == $rule->server->sshPort,
                $rule->fromIp,
            );

            $rule->delete();

            // TODO: CRITICAL! Am I sure there's nothing else to do here?

            event(new FirewallRuleDeletedEvent($server));
        }, 5);
    }
}
