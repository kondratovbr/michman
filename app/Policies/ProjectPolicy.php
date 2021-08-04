<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: CRITICAL! Cover with tests.

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

    public function deploy(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }
}
