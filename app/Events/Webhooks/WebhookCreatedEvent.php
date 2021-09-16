<?php declare(strict_types=1);

namespace App\Events\Webhooks;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebhookCreatedEvent extends AbstractWebhookEvent implements ShouldBroadcast
{
    //
}
