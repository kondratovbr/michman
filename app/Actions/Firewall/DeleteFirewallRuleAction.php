<?php declare(strict_types=1);

namespace App\Actions\Firewall;

use App\Jobs\FirewallRules\DeleteFirewallRuleJob;
use App\Models\FirewallRule;
use Illuminate\Support\Facades\DB;

class DeleteFirewallRuleAction
{
    public function execute(FirewallRule $rule): void
    {
        DB::transaction(function () use ($rule) {
            /** @var FirewallRule $rule */
            $rule = FirewallRule::query()
                ->with('server')
                ->lockForUpdate()
                ->findOrFail($rule->getKey());

            $rule->status = FirewallRule::STATUS_DELETING;
            $rule->save();

            DeleteFirewallRuleJob::dispatch($rule);
        }, 5);
    }
}
