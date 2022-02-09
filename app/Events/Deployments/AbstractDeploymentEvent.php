<?php declare(strict_types=1);

namespace App\Events\Deployments;

use App\Broadcasting\ProjectChannel;
use App\Events\AbstractEvent;
use App\Events\Interfaces\DeploymentEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Deployment;
use Illuminate\Broadcasting\Channel;

abstract class AbstractDeploymentEvent extends AbstractEvent implements DeploymentEvent
{
    use Broadcasted;

    protected int $deploymentKey;
    protected int $projectKey;

    public function __construct(Deployment $deployment)
    {
        $this->deploymentKey = $deployment->getKey();
        $this->projectKey = $deployment->projectId;
    }

    protected function getChannels(): Channel
    {
        return ProjectChannel::channelInstance($this->projectKey);
    }

    /** Retrieve the deployment from the database if it still exists. */
    public function deployment(): Deployment|null
    {
        /** @var Deployment|null $deployment */
        $deployment = Deployment::query()->find($this->deploymentKey);

        return $deployment;
    }
}
