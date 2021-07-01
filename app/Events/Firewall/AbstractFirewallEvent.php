<?php declare(strict_types=1);

namespace App\Events\Firewall;

use App\Events\AbstractServerEvent;
use App\Models\FirewallRule;

abstract class AbstractFirewallEvent extends AbstractServerEvent
{
    public FirewallRule $rule;

    public function __construct(FirewallRule $rule)
    {
        parent::__construct($rule->server);

        $this->rule = $rule->withoutRelations();
    }
}
