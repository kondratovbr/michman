<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Models\Webhook;
use App\Notifications\Projects\WebhookEnablingFailedNotification;
use App\States\Webhooks\Enabling;
use Illuminate\Support\Facades\DB;

class VerifyWebhookEnabledJob extends AbstractJob
{
    /** Delete the job if its models no longer exist. */
    public bool $deleteWhenMissingModels = true;

    protected Webhook $hook;

    public function __construct(Webhook $hook)
    {
        $this->setQueue('default');

        $this->hook = $hook->withoutRelations();
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $hook = $this->hook->freshLockForUpdate('calls');

            if (! $hook->state->is(Enabling::class))
                return;

            /*
             * If the webhook is still "enabling" it means we likely didn't receive
             * a "ping" event for some reason, i.e. something went wrong.
             */

            $hook->user->notify(new WebhookEnablingFailedNotification($hook->project));

            $hook->calls()->delete();
            $hook->delete();
        }, 5);
    }
}
