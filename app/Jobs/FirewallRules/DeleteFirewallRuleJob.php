<?php declare(strict_types=1);

namespace App\Jobs\FirewallRules;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\FirewallRule;
use App\Scripts\Root\DeleteFirewallRuleScript;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class DeleteFirewallRuleJob extends AbstractRemoteServerJob
{
    protected FirewallRule $rule;

    public function __construct(FirewallRule $rule)
    {
        parent::__construct($rule->server);

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
        }, 5);
    }
}
