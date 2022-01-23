<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\Interfaces\ProjectEvent;
use App\Events\Projects\ProjectUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatchProjectUpdatedEventListener extends AbstractEventListener implements ShouldQueue
{
    public function handle(ProjectEvent $event): void
    {
        $project = $event->project();

        if (is_null($project))
            return;

        ProjectUpdatedEvent::dispatch($project);
    }
}
