<?php declare(strict_types=1);

namespace App\Actions\Firewall;

use App\DataTransferObjects\FirewallRuleData;
use App\Jobs\Servers\AddFirewallRuleToServerJob;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests.

class StoreFirewallRuleAction
{
    /**
     * @param bool $sync Run the job synchronously.
     */
    public function execute(FirewallRuleData $data, Server $server, bool $sync = false): FirewallRule
    {
        DB::transaction(function () use($data, $server, $sync) {
            /** @var Server $server */
            $server = Server::query()
                ->lockForUpdate()
                ->findOrFail($server->getKey());

            $rule = $server->firewallRules()->create($data->toArray());

            if ($sync)
                AddFirewallRuleToServerJob::dispatchSync($rule);
            else
                AddFirewallRuleToServerJob::dispatch($rule);
        }, 5);
    }
}
