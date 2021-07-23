<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: CRITICAL! Cover with tests!

class ServerPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        // For now - no limits. Later will add subscription plan based limits here.
        return true;
    }

    public function update(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }
}
