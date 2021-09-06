<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkerPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function index(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    public function delete(User $user, Worker $worker): bool
    {
        return $user->is($worker->user);
    }
}