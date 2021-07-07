<?php declare(strict_types=1);

namespace App\Events\Firewall;

use App\Events\Servers\AbstractServerEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FirewallRuleDeletedEvent extends AbstractServerEvent implements ShouldBroadcast
{
    //
}
