<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Models\Webhook;
use App\States\Webhooks\Enabling;
use Illuminate\Support\Facades\DB;

class VerifyWebhookEnabledJob extends AbstractJob
{
    protected Webhook $hook;

    public function __construct(Webhook $hook)
    {
        $this->setQueue('default');

        $this->hook = $hook->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $hook = $this->hook->freshSharedLock();

            if (! $hook->state->is(Enabling::class))
                return;

            $hook->state->transitionTo();
        }, 5);
    }
}
