<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\Interfaces\Snaggable;
use App\Services\LogSnag;
use Illuminate\Contracts\Queue\ShouldQueue;

class SnagEventListener extends AbstractEventListener implements ShouldQueue
{
    public function handle(Snaggable $event, LogSnag $logSnag): void
    {
        $logSnag->publishEvent(
            channel: $event->getSnagChannel(),
            event: $event->getSnagEvent(),
            description: $event->getSnagDescription(),
            icon: $event->snagIcon ?? null,
            notify: $event->snagNotify ?? null,
        );
    }
}
