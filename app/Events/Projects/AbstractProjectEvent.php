<?php declare(strict_types=1);

namespace App\Events\Projects;

use App\Broadcasting\ProjectChannel;
use App\Broadcasting\ServerChannel;
use App\Broadcasting\UserChannel;
use App\Events\AbstractEvent;
use App\Models\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractProjectEvent extends AbstractEvent implements ShouldBroadcast
{
    public int $projectKey;

    public function __construct(Project $project)
    {
        $this->projectKey = $project->getKey();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        /** @var Project $project */
        $project = Project::query()->findOrFail($this->projectKey);

        $channels = [
            ProjectChannel::channelInstance($project),
            UserChannel::channelInstance($project->user),
        ];

        foreach ($project->servers as $server) {
            $channels[] = ServerChannel::channelInstance($server);
        }

        return $channels;
    }
}
