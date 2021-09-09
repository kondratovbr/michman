<?php declare(strict_types=1);

namespace App\Actions\Firewall;

use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\FirewallRules\AddFirewallRuleToServerJob;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

class StoreFirewallRuleAction
{
    /**
     * @param bool $sync Run the job synchronously.
     */
    public function execute(FirewallRuleData $data, Server $server, bool $sync = false): FirewallRule
    {
        return DB::transaction(function () use($data, $server, $sync) {
            /** @var FirewallRule $rule */
            $rule = $server->firewallRules()->firstOrNew($data->toArray());

            $rule->status = FirewallRule::STATUS_ADDING;
            $rule->save();

            if ($sync) {
                AddFirewallRuleToServerJob::dispatchSync($rule, true);
                $rule->refresh();
            } else {
                AddFirewallRuleToServerJob::dispatch($rule);
            }

            return $rule;
        }, 5);
    }
}
