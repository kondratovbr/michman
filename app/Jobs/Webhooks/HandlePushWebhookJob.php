<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Models\WebhookCall;

class HandlePushWebhookJob extends AbstractJob
{
    protected WebhookCall $call;

    public function __construct(WebhookCall $call)
    {
        $this->setQueue('default');

        $this->call = $call->withoutRelations();
    }

    public function handle(): void
    {
        // TODO: CRITICAL! Implement.

        //
    }
}
