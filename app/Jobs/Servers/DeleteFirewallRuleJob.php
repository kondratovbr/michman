<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\FirewallRule;

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
    public function handle(): void
    {
        // TODO: CRITICAL! Implement.

        //
    }
}
