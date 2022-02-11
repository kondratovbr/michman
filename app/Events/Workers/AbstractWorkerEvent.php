<?php declare(strict_types=1);

namespace App\Events\Workers;

use App\Broadcasting\ProjectChannel;
use App\Events\AbstractEvent;
use App\Events\Traits\Broadcasted;
use App\Models\Worker;
use Illuminate\Broadcasting\Channel;

abstract class AbstractWorkerEvent extends AbstractEvent
{
    use Broadcasted;

    protected int $workerKey;
    protected int $projectKey;

    public function __construct(Worker $worker)
    {
        $this->workerKey = $worker->getKey();
        $this->projectKey = $worker->projectId;
    }

    protected function getChannels(): Channel
    {
        return ProjectChannel::channelInstance($this->projectKey);
    }
}
