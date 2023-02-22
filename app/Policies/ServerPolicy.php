<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
{
    use HandlesAuthorization;

    public function index(User $user, User $subject): bool
    {
        return $subject->is($user);
    }

    public function create(User $user): bool
    {
        if (! $user->appEnabled())
            return false;

        if ($user->getPlan()->options['unlimited_servers'] ?? false)
            return true;

        if ($user->servers()->count() >= $user->getPlan()->options['servers'])
            return false;

        return true;
    }

    public function update(User $user, Server $server): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($server->user);
    }

    public function delete(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }
}
