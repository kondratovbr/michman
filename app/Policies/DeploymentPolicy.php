<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Deployment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeploymentPolicy
{
    use HandlesAuthorization;

    public function index(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function create(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function view(User $user, Deployment $deployment): bool
    {
        return $user->is($deployment->user);
    }
}
