<?php declare(strict_types=1);

namespace App\Notifications\Webhooks;

use App\Models\Project;
use App\Models\User;
use App\Models\Webhook;

class WebhookEnablingFailedNotification extends AbstractWebhookNotification
{
    protected Project $project;

    public function __construct(Webhook $hook, Project $project)
    {
        parent::__construct($hook);

        $this->project = $project->withoutRelations();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'webhookKey' => $this->hook->getKey(),
            'projectKey' => $this->project->getKey(),
        ];
    }

    protected static function dataForMessage(array $data = []): array
    {
        return [
            'project' => Project::query()->find($data['projectKey'])?->projectName ?? '',
        ];
    }
}
