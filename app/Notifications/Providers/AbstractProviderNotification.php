<?php declare(strict_types=1);

namespace App\Notifications\Providers;

use App\Models\Provider;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractProviderNotification extends AbstractNotification
{
    public function __construct(
        protected Provider $provider,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'providerKey' => $this->provider->getKey(),
        ];
    }
}
