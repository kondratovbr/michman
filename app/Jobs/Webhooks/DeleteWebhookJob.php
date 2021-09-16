<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Webhook;
use App\States\Webhooks\Deleting;
use Illuminate\Support\Facades\DB;

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

                // TODO: CRITICAL! Implement!
                
                //
            }

            $hook->delete();
        }, 5);
    }
}
