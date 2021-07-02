<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user is allowed to see the index of the database of a server.
     */
    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    /**
     * Determine whether a user is allowed to create a new database for a server.
     */
    public function create(User $user, Server $server): bool
    {
        return $user->is($server->user) && ! is_null($server->installedDatabase);
    }
}
