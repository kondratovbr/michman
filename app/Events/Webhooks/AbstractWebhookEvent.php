<?php declare(strict_types=1);

namespace App\Events\Webhooks;

use App\Events\Projects\AbstractProjectEvent;
use App\Models\Webhook;

abstract class AbstractWebhookEvent extends AbstractProjectEvent
{
    public int $webhookKey;

    public function __construct(Webhook $hook)
    {
        parent::__construct($hook->project);

        $this->webhookKey = $hook->getKey();
    }
}
