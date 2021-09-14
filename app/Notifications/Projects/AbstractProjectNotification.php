<?php declare(strict_types=1);

namespace App\Notifications\Projects;

use App\Models\Project;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractProjectNotification extends AbstractNotification
{
    public function __construct(
        protected Project $project,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'projectKey' => $this->project->getKey(),
        ];
    }

    /**
     * Retrieve the project from the database.
     */
    protected static function project(array $data): Project|null
    {
        /** @var Project|null $project */
        $project = Project::query()->find($data['projectKey']);

        return $project;
    }
}
