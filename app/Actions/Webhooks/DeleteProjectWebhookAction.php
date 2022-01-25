<?php declare(strict_types=1);

namespace App\Actions\Webhooks;

use App\Jobs\Webhooks\DeleteWebhookJob;
use App\Models\Webhook;
use App\States\Webhooks\Deleting;
use Illuminate\Support\Facades\DB;

class DeleteProjectWebhookAction
{
    public function execute(Webhook $hook, bool $returnJob = false): DeleteWebhookJob|null
    {
        return DB::transaction(function () use ($hook, $returnJob): DeleteWebhookJob|null {
            $hook = $hook->freshLockForUpdate();

            if (! $hook->state->canTransitionTo(Deleting::class))
                return null;

            $hook->state->transitionTo(Deleting::class);

            if ($returnJob)
                return new DeleteWebhookJob($hook);

            DeleteWebhookJob::dispatch($hook);

            return null;
        }, 5);
    }
}
