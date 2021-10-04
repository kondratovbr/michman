<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesWebhooks;
use App\Jobs\Traits\IsInternal;
use App\Models\WebhookCall;
use App\States\Webhooks\Enabled;

class HandlePingWebhookJob extends AbstractJob
{
    use HandlesWebhooks;
    use IsInternal;

    protected WebhookCall $call;

    public function __construct(WebhookCall $call)
    {
        $this->setQueue('default');

        $this->call = $call->withoutRelations();
        $this->callType = 'ping';
    }

    public function execute(): void
    {
        $this->call->webhook->state->transitionToIfCan(Enabled::class);
    }
}
