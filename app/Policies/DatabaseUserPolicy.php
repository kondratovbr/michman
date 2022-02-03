<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabaseUserPolicy
{
    use HandlesAuthorization;

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function create(User $user, Server $server): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($server->user) && ! is_null($server->installedDatabase);
    }

    public function update(User $user, DatabaseUser $databaseUser): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($databaseUser->user);
    }

    public function delete(User $user, DatabaseUser $databaseUser): bool
    {
        return $user->is($databaseUser->user) && $databaseUser->tasks == 0;
    }
}
