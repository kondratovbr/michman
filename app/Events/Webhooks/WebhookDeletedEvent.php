<?php declare(strict_types=1);

namespace App\Events\Webhooks;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebhookDeletedEvent extends AbstractWebhookEvent implements ShouldBroadcast
{
    //
}
