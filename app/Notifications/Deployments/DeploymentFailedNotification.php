<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

class DeploymentFailedNotification extends AbstractDeploymentNotification
{
    /*
     * TODO: CRITICAL! Test if this works!
     */
    protected bool $broadcast = true;

    //
}
