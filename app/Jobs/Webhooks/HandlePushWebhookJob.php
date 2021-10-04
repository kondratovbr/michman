<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Actions\Projects\DeployProjectAction;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesWebhooks;
use App\Jobs\Traits\IsInternal;
use App\Models\Project;
use App\Models\WebhookCall;

class HandlePushWebhookJob extends AbstractJob
{
    use HandlesWebhooks;
    use IsInternal;

    protected WebhookCall $call;

    public function __construct(WebhookCall $call)
    {
        $this->setQueue('default');

        $this->call = $call->withoutRelations();
        $this->callType = 'push';
    }

    public function execute(DeployProjectAction $action): void
    {
        /** @var Project $project */
        $project = $this->call->webhook->project()->sharedLock()->firstOrFail();

        $action->execute($project, $this->call->payload['after']);

        // TODO: CRITICAL! Notify the user about a triggered deployment via email. Show in the UI (by storing in the DB) as well.
    }
}
