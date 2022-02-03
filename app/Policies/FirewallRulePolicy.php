<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\FirewallRule;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FirewallRulePolicy
{
    use HandlesAuthorization;

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->provider->user);
    }

    public function create(User $user, Server $server): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($server->user);
    }

    public function delete(User $user, FirewallRule $rule): bool
    {
        return $user->is($rule->user) && $rule->canDelete;
    }
}
