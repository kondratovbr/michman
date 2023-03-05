<?php declare(strict_types=1);

namespace App\Events\Projects;

use App\Events\Interfaces\Snaggable;
use App\Services\LogSnag\SnagChannel;
use App\Services\LogSnag\SnagEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectCreatedEvent extends AbstractProjectEvent implements ShouldBroadcast, Snaggable
{
    public bool $snagNotify = true;
    public string|null $snagIcon = '🏗️';

    public function getSnagChannel(): SnagChannel
    {
        return SnagChannel::PROJECTS;
    }

    public function getSnagEvent(): SnagEvent
    {
        return SnagEvent::PROJECT_CREATED;
    }

    public function getSnagDescription(): string|null
    {
        return "Created Project ID $this->projectKey";
    }
}
