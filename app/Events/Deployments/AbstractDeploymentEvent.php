<?php declare(strict_types=1);

namespace App\Events\Deployments;

use App\Events\Projects\AbstractProjectEvent;
use App\Models\Deployment;

abstract class AbstractDeploymentEvent extends AbstractProjectEvent
{
    protected int $deploymentKey;

    public function __construct(Deployment $deployment)
    {
        parent::__construct($deployment->project);

        $this->deploymentKey = $deployment->getKey();
    }
}
