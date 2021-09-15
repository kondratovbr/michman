<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;
use App\Models\Webhook;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class EnableWebhookAction
{
    public function execute(Project $project): void
    {
        // TODO: CRITICAL! CONTINUE. Implement.

        DB::transaction(function () use ($project) {
            $project = $project->freshSharedLock();

            if (isset($project->webhook))
                return;

            $hook = $project->webhook()->create([
                'type' => 'push',
                'status' => Webhook::STATUS_ENABLING,
            ]);

            //
        }, 5);
    }
}
