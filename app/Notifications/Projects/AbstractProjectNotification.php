<?php declare(strict_types=1);

namespace App\Notifications\Projects;

use App\Models\Project;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractProjectNotification extends AbstractNotification
{
    public function __construct(
        protected Project $project,
    ) {}

    public function toArray(User $notifiable): array
    {
        return [
            'projectKey' => $this->project->getKey(),
        ];
    }
}
