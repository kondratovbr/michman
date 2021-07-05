<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Database;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user is allowed to see the index of databases on a server.
     */
    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    /**
     * Determine whether a user is allowed to create a new database on a server.
     */
    public function create(User $user, Server $server): bool
    {
        return $user->is($server->user) && ! is_null($server->installedDatabase);
    }

    /**
     * Determine whether a user is allowed to delete a database from a server.
     */
    public function delete(User $user, Database $database): bool
    {
        return $user->is($database->user);
    }
}
