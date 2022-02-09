<?php declare(strict_types=1);

namespace App\Events\Projects;

use App\Broadcasting\ProjectChannel;
use App\Broadcasting\ServerChannel;
use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Events\Interfaces\ProjectEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Project;

abstract class AbstractProjectEvent extends AbstractEvent implements ProjectEvent
{
    use Broadcasted;

    protected int $projectKey;
    protected int $userKey;

    public function __construct(Project $project)
    {
        $this->projectKey = $project->getKey();
        $this->userKey = $project->userId;
    }

    protected function getChannels(): array
    {
        $channels = [
            ProjectChannel::channelInstance($this->projectKey),
            UserChannel::channelInstance($this->userKey),
        ];

        /** @var Project $project */
        $project = Project::query()->find($this->projectKey);

        if (! $project)
            return $channels;

        foreach ($project->servers as $server) {
            $channels[] = ServerChannel::channelInstance($server);
        }

        return $channels;
    }

    public function project(): Project|null
    {
        /** @var Project|null $project */
        $project = Project::query()->find($this->projectKey);

        return $project;
    }
}
