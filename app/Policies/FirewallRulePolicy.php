<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\FirewallRule;
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

    /**
     * Determine whether a user is allowed to create a firewall rule for a server.
     */
    public function create(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    /**
     * Determine whether a user is allowed to delete a firewall rule from a server.
     */
    public function delete(User $user, FirewallRule $rule): bool
    {
        return $user->is($rule->user) && $rule->canDelete;
    }
}
