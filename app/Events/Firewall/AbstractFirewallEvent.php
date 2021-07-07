<?php declare(strict_types=1);

namespace App\Events\Firewall;

use App\Events\Servers\AbstractServerEvent;
use App\Models\FirewallRule;

abstract class AbstractFirewallEvent extends AbstractServerEvent
{
    public int $ruleKey;

    public function __construct(FirewallRule $rule)
    {
        parent::__construct($rule->server);

        $this->ruleKey = $rule->getKey();
    }
}
