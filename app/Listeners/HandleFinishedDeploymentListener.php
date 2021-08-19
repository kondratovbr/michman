<?php declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HandleFinishedDeploymentListener extends AbstractEventListener implements ShouldQueue
{
    public function handle(): void
    {
        Log::error('HandleFinishedDeploymentListener called, but not implemented. And maybe wasn\'t even supposed to exist.');

        //
    }
}
