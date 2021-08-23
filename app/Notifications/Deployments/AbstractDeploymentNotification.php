<?php declare(strict_types=1);

namespace App\Notifications\Deployments;

use App\Models\Deployment;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractDeploymentNotification extends AbstractNotification
{
    public function __construct(
        protected Deployment $deployment,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'deploymentKey' => $this->deployment->getKey(),
        ];
    }

    public static function message(array $data = []): string
    {
        /** @var Deployment|null $deployment */
        $deployment = Deployment::query()->find($data['deploymentKey']);

        return static::getMessage($deployment);
    }

    abstract protected static function getMessage(Deployment|null $deployment): string;
}
