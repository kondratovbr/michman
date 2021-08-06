<?php declare(strict_types=1);

namespace App\Actions\Firewall;

use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\FirewallRules\AddFirewallRuleToServerJob;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

/*
 * TODO: CRITICAL! Make sure a user cannot create duplicate rules.
 */

class StoreFirewallRuleAction
{
    /**
     * @param bool $sync Run the job synchronously.
     */
    public function execute(FirewallRuleData $data, Server $server, bool $sync = false): FirewallRule
    {
        return DB::transaction(function () use($data, $server, $sync) {

            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($server->getKey());

            $attributes = $data->toArray();

            $attributes['status'] = FirewallRule::STATUS_ADDING;

            $rule = $server->firewallRules()->create($attributes);

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
