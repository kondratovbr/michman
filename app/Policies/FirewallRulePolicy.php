<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FirewallRulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user is allowed to see the index of firewall rules for a server.
     */
    public function index(User $user, Server $server): bool
    {
        return $user->is($server->provider->owner);
    }
}
