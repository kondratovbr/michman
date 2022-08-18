<?php declare(strict_types=1);

namespace App\Jobs\FirewallRules;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\FirewallRule;
use App\Scripts\Root\DeleteFirewallRuleScript;
use Illuminate\Support\Facades\DB;

class DeleteFirewallRuleJob extends AbstractRemoteServerJob
{
    protected FirewallRule $rule;

    public function __construct(FirewallRule $rule)
    {
        parent::__construct($rule->server);

        $this->rule = $rule->withoutRelations();
    }

    public function handle(DeleteFirewallRuleScript $deleteFirewallRule): void
    {
        DB::transaction(function () use ($deleteFirewallRule) {
            $rule = $this->rule->freshLockForUpdate();
            $server = $this->server->freshSharedLock();

            $deleteFirewallRule->execute(
                $server,
                $rule->port,
                $rule->port == $server->sshPort,
                $rule->fromIp,
            );

            $rule->purge();
        });
    }
}
