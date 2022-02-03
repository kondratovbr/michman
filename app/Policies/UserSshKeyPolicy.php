<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserSshKey;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: IMPORTANT! Cover with tests.

class UserSshKeyPolicy
{
    use HandlesAuthorization;

    public function index(User $user, User $subject): bool
    {
        return $user->is($subject);
    }

    public function create(User $user, User $subject): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($subject);
    }

    public function update(User $user, UserSshKey $key): bool
    {
        if (! $user->appEnabled())
            return false;

        return $user->is($key->user);
    }

    public function delete(User $user, UserSshKey $key): bool
    {
        return $user->is($key->user);
    }
}
