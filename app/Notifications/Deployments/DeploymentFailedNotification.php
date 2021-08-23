<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

class DeploymentFailedNotification extends AbstractDeploymentNotification
{
    protected bool $broadcast = true;

    //
}
