<?php declare(strict_types=1);

namespace App\Events\Firewall;

use App\Broadcasting\ServerChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\FirewallRule;
use Illuminate\Broadcasting\Channel;

abstract class AbstractFirewallEvent extends AbstractEvent
{
    use Broadcasted;

    protected int $serverKey;

    public function __construct(FirewallRule $rule)
    {
        $this->serverKey = $rule->serverId;
    }

    protected function getChannels(): Channel
    {
        return ServerChannel::channelInstance($this->serverKey);
    }
}
