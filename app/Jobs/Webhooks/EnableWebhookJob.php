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
        parent::__construct();

        $this->hook = $hook->withoutRelations();
    }

    public function handle(): void
    {
        $api = $this->hook->project->vcsProvider->api();

        DB::transaction(function () use ($api) {
            $hook = $this->hook->freshLockForUpdate();

            if (! $hook->state->canTransitionTo(Enabled::class))
                return;

            $hookData = $api->addWebhookSafelyPush(
                $hook->repo,
                $hook->url,
                $hook->secret,
            );

            $hook->externalId = $hookData->id;

            $hook->save();

            if ($api->dispatchesPingWebhookCalls()) {
                // We'll wait for a "ping" event to be sent and handled and then verify that it worked in a separate job.
                VerifyWebhookEnabledJob::dispatch($hook)->delay(120);
            } else {
                // If the service doesn't dispatch "ping" calls we'll just consider webhook enabled right now and hope for the best.
                $hook->state->transitionToIfCan(Enabled::class);
            }
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
