<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Models\WebhookCall;
use App\States\Webhooks\Enabled;
use Illuminate\Support\Facades\DB;

class HandlePingWebhookJob extends AbstractJob
{
    protected WebhookCall $call;

    public function __construct(WebhookCall $call)
    {
        $this->setQueue('default');

        $this->call = $call->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $call = $this->call->freshLockForUpdate();

            if (! $call->webhook->state->canTransitionTo(Enabled::class))
                return;

            $call->webhook->state->transitionTo(Enabled::class);
        }, 5);
    }
}
