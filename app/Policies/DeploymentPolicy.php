<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: CRITICAL! Cover with tests!

class DeploymentPolicy
{
    use HandlesAuthorization;

    public function index(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function deploy(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }
}
