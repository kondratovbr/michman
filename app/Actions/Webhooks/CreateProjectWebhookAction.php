<?php declare(strict_types=1);

namespace App\Actions\Webhooks;

use App\Jobs\Webhooks\EnableWebhookJob;
use App\Models\Project;
use App\States\Webhooks\Enabling;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! Cover with tests!

class CreateProjectWebhookAction
{
    public function execute(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $project = $project->freshSharedLock();

            if (isset($project->webhook))
                return;

            $hook = $project->webhook()->create([
                'type' => 'push',
                'state' => Enabling::class,
            ]);

            EnableWebhookJob::dispatch($hook);
        }, 5);
    }
}
