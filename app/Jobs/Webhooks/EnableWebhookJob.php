<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithVcsProviders;
use App\Models\Webhook;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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

            $api = $hook->project->vcsProvider->api();

            $hookData = $api->addWebhookPush($hook->project->repo, $hook->payloadUrl);

            if (is_null($hookData->id))
                throw new RuntimeException('Received no external ID after creating a webhook on ' . $hook->project->vcsProvider->provider);

            $hook->externalId = $hookData->id;

            $hook->status = Webhook::STATUS_ENABLED;

            $hook->save();
        }, 5);
    }
}
