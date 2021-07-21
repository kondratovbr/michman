<?php declare(strict_types=1);

namespace App\Events\Projects;

use App\Broadcasting\ProjectChannel;
use App\Events\AbstractEvent;
use App\Models\Project;
use Illuminate\Broadcasting\Channel;

/*
 * TODO: CRITICAL! This event gets broadcasted only on ProjectChannel due to the project probably not existing anymore - we can't get the servers it was attached to. Is this OK?
 */

class ProjectDeletedEvent extends AbstractEvent
{
    protected int $projectKey;

    public function __construct(Project $project)
    {
        $this->projectKey = $project->getKey();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        return ProjectChannel::channelInstance($this->projectKey);
    }
}
