<?php declare(strict_types=1);

namespace App\Policies;

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
        return $user->is($server->provider->owner);
    }

    /**
     * Determine whether a user is allowed to install a new Python instance on a server.
     */
    public function create(User $user, Server $server, string $version): bool
    {
        return $user->is($server->provider->owner)
            && $server->pythons()->where('version', $version)->count() === 0;
    }
}
