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
        // For now - no limits. Later will add subscription plan based limits here.
        return true;
    }

    public function update(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function delete(User $user, Server $server): bool
    {
        // TODO: CRITICAL! Is this correct? Need to check anything else?

        return $user->is($server->user);
    }
}
