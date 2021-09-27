<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesWebhooks;
use App\Jobs\Traits\IsInternal;
use App\Models\WebhookCall;
use App\States\Webhooks\Enabled;
use Illuminate\Support\Facades\DB;

class HandlePingWebhookJob extends AbstractJob
{
    use HandlesWebhooks;
    use IsInternal;

    protected WebhookCall $call;

    public function __construct(WebhookCall $call)
    {
        $this->setQueue('default');

        $this->call = $call->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $call = $this->call->freshLockForUpdate('webhook');

            $this->verifyHookCallType($call, 'ping');

            $call->webhook->state->transitionToIfCan(Enabled::class);

            $call->processed = true;
            $call->save();
        }, 5);
    }
}
