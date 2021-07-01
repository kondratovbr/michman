<?php declare(strict_types=1);

namespace App\Events\Firewall;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FirewallRuleAddedEvent extends AbstractFirewallEvent implements ShouldBroadcast
{
    //
}
