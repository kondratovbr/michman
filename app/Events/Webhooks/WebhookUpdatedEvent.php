<?php declare(strict_types=1);

namespace App\Events\Webhooks;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebhookUpdatedEvent extends AbstractWebhookEvent implements ShouldBroadcast
{
    //
}
