<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\Interfaces\Snaggable;
use App\Services\LogSnag;
use App\Services\LogSnag\SnagChannel;
use App\Services\LogSnag\SnagEvent;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;

class SnagEventListener extends AbstractEventListener implements ShouldQueue
{
    public function __construct(
        private readonly LogSnag $snag,
    ) {}

    public function handle(Snaggable|Verified $event): void
    {
        if (App::isLocal())
            return;

        if ($event instanceof Snaggable) {
            $this->snagGenericEvent($event);
            return;
        }

        if ($event instanceof Verified) {
            $this->snagVerifiedEvent($event);
            return;
        }
    }

    private function snagGenericEvent(Snaggable $event): void
    {
        $this->snag->publishEvent(
            channel: App::isProduction() ? $event->getSnagChannel() : SnagChannel::TEST,
            event: $event->getSnagEvent(),
            description: $event->getSnagDescription(),
            icon: $event->snagIcon ?? null,
            notify: $event->snagNotify ?? false,
        );
    }

    private function snagVerifiedEvent(Verified $event): void
    {
        $this->snag->publishEvent(
            channel: App::isProduction() ? SnagChannel::USERS : SnagChannel::TEST,
            event: SnagEvent::USER_REGISTERED,
            description: "Registered and verified User ID {$event->user->getAuthIdentifier()}",
            icon: "🧍",
            notify: true,
        );
    }
}
