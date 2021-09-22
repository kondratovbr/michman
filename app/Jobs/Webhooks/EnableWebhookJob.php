<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Webhook;
use App\Notifications\Projects\WebhookEnablingFailedNotification;
use App\States\Webhooks\Enabled;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// TODO: CRITICAL! CONTINUE. Test. Also, I should probably monitor for the "ping" event and somehow use it to verify that the hook is working.

// TODO: CRITICAL! Cover with tests!

/*
 * TODO: CRITICAL! I should cleanup webhook model if this process fails and notify the user.
 */

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

            $api = $hook->project->vcsProvider->api();

            $hookData = $api->addWebhookSafelyPush(
                $hook->repo,
                $hook->payloadUrl,
                $hook->secret,
            );

            if (is_null($hookData->id))
                throw new RuntimeException('Received no external ID after creating a webhook on ' . $hook->project->vcsProvider->provider);

            $hook->externalId = $hookData->id;

            $hook->save();

            // We'll wait for a "ping" event to be sent and handled and then verify that it worked in a separate job.
            VerifyWebhookEnabledJob::dispatch($hook)->delay(60);
        }, 5);
    }

    public function failed(): void
    {
        DB::transaction(function () {
            $hook = $this->hook->freshLockForUpdate();

            $hook->user->notify(new WebhookEnablingFailedNotification($hook->project));

            $hook->delete();
        }, 10);
    }
}
