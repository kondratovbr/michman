<?php declare(strict_types=1);

namespace App\Events\Servers;

use App\Events\Interfaces\Snaggable;
use App\Services\LogSnag\SnagChannel;
use App\Services\LogSnag\SnagEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerCreatedEvent extends AbstractServerEvent implements ShouldBroadcast, Snaggable
{
    public bool $snagNotify = true;
    public string|null $snagIcon = '🖥️';

    public function getSnagChannel(): SnagChannel
    {
        return SnagChannel::SERVERS;
    }

    public function getSnagEvent(): SnagEvent
    {
        return SnagEvent::SERVER_CREATED;
    }

    public function getSnagDescription(): string|null
    {
        return "Created Server ID $this->serverKey";
    }
}
