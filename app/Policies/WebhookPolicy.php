<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }
}
