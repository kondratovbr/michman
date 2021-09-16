<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Webhook;
use App\Notifications\Projects\WebhookDeletingFailedNotification;
use App\States\Webhooks\Deleting;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! CONTINUE. Test.

// TODO: CRITICAL! Cover with tests!

class DeleteWebhookJob extends AbstractJob
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

            if (! $hook->state->is(Deleting::class))
                return;

            if (isset($hook->externalId)) {
                $api = $hook->project->vcsProvider->api();

                $api->deleteWebhookIfExistsPush($hook->repo, $hook->payloadUrl);
            }

            $hook->delete();
        }, 5);
    }

    public function failed(): void
    {
        /*
         * Failure means we most likely don't have access to the repo anymore,
         * so we just remove the hook model from the DB and notify the user.
         */
        DB::transaction(function () {
            $hook = $this->hook->freshLockForUpdate();

            $this->hook->user->notify(new WebhookDeletingFailedNotification($this->hook->project));

            $hook->delete();
        }, 5);
    }
}
