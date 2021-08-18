<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

use App\Models\Deployment;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractDeploymentNotification extends AbstractNotification
{
    public function __construct(
        protected Deployment $deployment,
    ) {}

    public function toArray(User $notifiable): array
    {
        return [
            'deploymentKey' => $this->deployment->getKey(),
        ];
    }
}
