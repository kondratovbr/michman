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
        if ($user->onTrial())
            return true;

        if (! $user->subscribed())
            return false;

        if ($user->servers()->count() > 0 && ! ($user->sparkPlan()->options['unlimited_servers'] ?? false))
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
