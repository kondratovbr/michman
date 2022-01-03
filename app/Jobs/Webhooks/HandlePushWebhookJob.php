<?php declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Actions\Projects\DeployProjectAction;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\HandlesWebhooks;
use App\Jobs\Traits\IsInternal;
use App\Models\Project;
use App\Models\WebhookCall;
use RuntimeException;

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

        if (! $this->validates())
            return;

        $hash = $this->call->webhook->service()->pushedCommitHash($this->call->payload);

        $action->execute($project, $hash, true);
    }

    protected function validates(): bool
    {
        $project = $this->call->webhook->project;
        $service = $this->call->webhook->service();

        $branch = $service->pushedBranch($this->call->payload);

        if (empty($branch) || $branch != $project->branch)
            return false;

        if (empty($service->pushedCommitHash($this->call->payload)))
            throw new RuntimeException('No commit hash found in the push webhook event payload.');

        return true;
    }
}
