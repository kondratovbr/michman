<?php declare(strict_types=1);

namespace App\Events\Deployments;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeploymentUpdatedEvent extends AbstractDeploymentEvent implements ShouldBroadcast
{
    //
}
