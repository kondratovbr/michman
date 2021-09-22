<?php declare(strict_types=1);

namespace App\Actions\Webhooks;

use App\Jobs\Webhooks\EnableWebhookJob;
use App\Models\Project;
use App\States\Webhooks\Enabling;
use App\Support\Str;
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
                'provider' => $project->vcsProvider->webhookProvider,
                'type' => 'push',
                'secret' => Str::random(40),
                'state' => Enabling::class,
            ]);

            EnableWebhookJob::dispatch($hook);
        }, 5);
    }
}
