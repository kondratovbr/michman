<?php declare(strict_types=1);

namespace App\Events\Deployments;

use App\Events\Interfaces\Snaggable;
use App\Services\LogSnag\SnagChannel;
use App\Services\LogSnag\SnagEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeploymentFinishedEvent extends AbstractDeploymentEvent implements ShouldBroadcast, Snaggable
{
    public bool $snagNotify = true;
    public string|null $snagIcon = '🏗️';

    public function getSnagChannel(): SnagChannel
    {
        return SnagChannel::PROJECTS;
    }

    public function getSnagEvent(): SnagEvent
    {
        return SnagEvent::DEPLOYMENT_FINISHED;
    }

    public function getSnagDescription(): string|null
    {
        return "Finished deployment of Project ID $this->projectKey";
    }
}
