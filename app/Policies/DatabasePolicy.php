<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Database;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabasePolicy
{
    use HandlesAuthorization;

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function create(User $user, Server $server): bool
    {
        if (! $user->is($server->user))
            return false;

        if (empty($server->installedDatabase))
            return false;

        return true;
    }

    public function delete(User $user, Database $database): bool
    {
        if (! $user->is($database->user))
            return false;

        if ($database->tasks != 0)
            return false;

        return true;
    }
}
