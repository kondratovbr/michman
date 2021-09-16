<?php declare(strict_types=1);

namespace App\Actions\Webhooks;

use App\Jobs\Webhooks\DeleteWebhookJob;
use App\Models\Webhook;
use App\States\Webhooks\Deleting;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class DeleteProjectWebhookAction
{
    public function execute(Webhook $hook): void
    {
        DB::transaction(function () use ($hook) {
            $hook = $hook->freshLockForUpdate();

            if (! $hook->state->canTransitionTo(Deleting::class))
                return;

            $hook->state->transitionTo(Deleting::class);

            DeleteWebhookJob::dispatch($hook);
        }, 5);
    }
}
