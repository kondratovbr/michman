<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Python;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PythonPolicy
{
    use HandlesAuthorization;

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function create(User $user, Server $server, string $version): bool
    {
        return $user->is($server->user)
            && $server->pythons()->where('version', $version)->count() === 0;
    }

    public function update(User $user, Python $python): bool
    {
        return $user->is($python->user);
    }

    public function delete(User $user, Python $python): bool
    {
        return $user->is($python->user);
    }
}
