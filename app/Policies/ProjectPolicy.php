<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function create(User $user, Server $server): bool
    {
        if (! $user->appEnabled())
            return false;

        if ($user->sparkPlan()->options['unlimited_projects'] ?? false)
            return true;

        if ($user->projects()->count() >= $user->sparkPlan()->options['projects'])
            return false;

        return $user->is($server->user);
    }

    public function view(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function update(User $user, Project $project): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($project->user);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }
}
