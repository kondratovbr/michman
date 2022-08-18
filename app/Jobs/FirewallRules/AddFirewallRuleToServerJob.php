<?php declare(strict_types=1);

namespace App\Jobs\FirewallRules;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\FirewallRule;
use App\Scripts\Root\AddFirewallRuleScript;
use Illuminate\Support\Facades\DB;

class AddFirewallRuleToServerJob extends AbstractRemoteServerJob
{
    protected FirewallRule $rule;

    public function __construct(FirewallRule $rule, bool $sync = false)
    {
        parent::__construct($rule->server, $sync);

        $this->rule = $rule->withoutRelations();
    }

    public function handle(AddFirewallRuleScript $addFirewallRule): void
    {
        DB::transaction(function () use ($addFirewallRule) {
            $rule = $this->rule->freshLockForUpdate();
            $server = $this->server->freshSharedLock();

            $addFirewallRule->execute(
                $server,
                $rule->port,
                // We're going to "limit" the SSH port using the UFW built-in config as an additional brute-force protection for SSH.
                // TODO: IMPORTANT! Should I not do this? Or should I account for this in my server jobs somehow instead? I.e. properly rate-limit them.
                $rule->port == $server->sshPort,
                $rule->fromIp,
            );

            $rule->status = FirewallRule::STATUS_ADDED;
            $rule->save();
        }, 5);
    }
}
