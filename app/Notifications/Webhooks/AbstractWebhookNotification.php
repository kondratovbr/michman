<?php declare(strict_types=1);

namespace App\Notifications\Webhooks;

use App\Models\User;
use App\Models\Webhook;
use App\Notifications\AbstractNotification;

abstract class AbstractWebhookNotification extends AbstractNotification
{
    public function __construct(
        protected Webhook $hook,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'webhookKey' => $this->hook->getKey(),
        ];
    }

    /** Retrieve the webhook model from the database. */
    protected static function webhook(array $data): Webhook|null
    {
        /** @var Webhook|null $webhook */
        $webhook = Webhook::query()->find($data['webhookKey']);

        return $webhook;
    }

    protected static function dataForMessage(array $data = []): array
    {
        return [
            'project' => static::webhook($data)->project->projectName,
        ];
    }
}
