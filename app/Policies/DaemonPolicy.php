<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Daemon;
use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DaemonPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function index(User $user, Server $server): bool
    {
        return $user->is($server->user);
    }

    public function update(User $user, Daemon $daemon): bool
    {
        return $user->is($daemon->user);
    }

    public function delete(User $user, Daemon $daemon): bool
    {
        return $user->is($daemon->user);
    }
}
