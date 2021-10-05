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
        return $user->is($server->user) && ! is_null($server->installedDatabase);
    }

    public function delete(User $user, Database $database): bool
    {
        return $user->is($database->user) && $database->tasks == 0;
    }
}
