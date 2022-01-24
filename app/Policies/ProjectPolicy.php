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
        return $user->is($server->user);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function delete(User $user, Project $project): bool
    {
        // TODO: CRITICAL! Is this correct? Need to check anything else?

        return $user->is($project->user);
    }
}
