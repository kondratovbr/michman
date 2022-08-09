<?php declare(strict_types=1);

namespace App\Events\Deployments;

use App\Broadcasting\ProjectChannel;
use App\Events\AbstractEvent;
use App\Events\Interfaces\DeploymentEvent;
use App\Events\Interfaces\ProjectEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Broadcasting\Channel;

abstract class AbstractDeploymentEvent extends AbstractEvent implements DeploymentEvent, ProjectEvent
{
    use Broadcasted;

    public int $deploymentKey;
    public int $projectKey;

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

    /** Retrieve the project that was being deployed by this event. */
    public function project(): Project|null
    {
        return $this->deployment()?->project;
    }
}
