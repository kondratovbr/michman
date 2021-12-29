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
        if (! $user->is($server->user))
            return false;

        return $server->pythons()->where('version', $version)->count() === 0;
    }

    public function update(User $user, Python $python): bool
    {
        return $user->is($python->user);
    }

    public function delete(User $user, Python $python): bool
    {
        if (! $user->is($python->user))
            return false;

        if ($python->server->pythons()->count() == 1)
            return false;

        if ($python->isInUse())
            return false;

        return true;
    }
}
