<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Webhook;
use App\Notifications\Projects\WebhookEnablingFailedNotification;
use App\States\Webhooks\Enabled;
use Illuminate\Support\Facades\DB;

class EnableWebhookJob extends AbstractJob
{
    use InteractsWithVcsProviders;

    protected Webhook $hook;

    public function __construct(Webhook $hook)
    {
        $this->setQueue('providers');

        $this->hook = $hook->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $hook = $this->hook->freshLockForUpdate();

            if (! $hook->state->canTransitionTo(Enabled::class))
                return;

            /*
             * TODO: CRITICAL! CONTINUE. There's an issue with calls like this.
             *       This call may refresh the access token, but
             *       if the later code fails it won't be saved
             *       due to it being run in a single transaction.
             */

            $api = $hook->project->vcsProvider->api();

            $hookData = $api->addWebhookSafelyPush(
                $hook->repo,
                $hook->url,
                $hook->secret,
            );

            $hook->externalId = $hookData->id;

            $hook->save();

            // We'll wait for a "ping" event to be sent and handled and then verify that it worked in a separate job.
            VerifyWebhookEnabledJob::dispatch($hook)->delay(120);
        }, 5);
    }

    public function failed(): void
    {
        DB::transaction(function () {
            $hook = $this->hook->freshLockForUpdate('calls');

            $hook->user->notify(new WebhookEnablingFailedNotification($hook->project));

            $hook->calls()->delete();
            $hook->delete();
        }, 10);
    }
}
