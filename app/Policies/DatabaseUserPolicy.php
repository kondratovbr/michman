<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: CRITICAL! Cover with tests!

class DatabaseUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user is allowed to see the index of database users on a server.
     */
    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    /**
     * Determine whether a user is allowed to create a new database user on a server.
     */
    public function create(User $user, Server $server): bool
    {
        return $user->is($server->user) && ! is_null($server->installedDatabase);
    }

    /**
     * Determine whether a user is allowed to delete a database user from a server.
     */
    public function delete(User $user, DatabaseUser $databaseUser): bool
    {
        return $user->is($databaseUser->user) && $databaseUser->tasks == 0;
    }
}
