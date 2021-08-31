<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: CRITICAL! Covert with tests.

class CertificatePolicy
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
}
