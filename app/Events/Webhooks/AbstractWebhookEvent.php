<?php declare(strict_types=1);

namespace App\Events\Webhooks;

use App\Broadcasting\ProjectChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Webhook;
use Illuminate\Broadcasting\Channel;

abstract class AbstractWebhookEvent extends AbstractEvent
{
    use Broadcasted;

    public int $webhookKey;
    public int $projectKey;

    public function __construct(Webhook $hook)
    {
        $this->webhookKey = $hook->getKey();
        $this->projectKey = $hook->projectId;
    }

    protected function getChannels(): Channel
    {
        return ProjectChannel::channelInstance($this->projectKey);
    }
}
