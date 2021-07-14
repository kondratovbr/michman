<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Python;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PythonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user is allowed to see the index of Python versions installed on a server.
     */
    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    /**
     * Determine whether a user is allowed to install a new Python instance on a server.
     */
    public function create(User $user, Server $server, string $version): bool
    {
        return $user->is($server->user)
            && $server->pythons()->where('version', $version)->count() === 0;
    }

    /**
     * Determine whether a user is allowed to update a Python model and its corresponding instance on a server.
     */
    public function update(User $user, Python $python): bool
    {
        return $user->is($python->user);
    }

    /**
     * Determine whether a user is allowed to delete a Python instance from a server.
     */
    public function delete(User $user, Python $python): bool
    {
        // TODO: CRITICAL! Put a correct check here and don't forget to cover with a test.
        return false;

        return $user->is($python->user);
    }
}
