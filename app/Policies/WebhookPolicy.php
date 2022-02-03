<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Project $project): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($project->user);
    }

    public function delete(User $user, Webhook $hook): bool
    {
        return $user->is($hook->user);
    }
}
