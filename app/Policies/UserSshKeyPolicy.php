<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserSshKey;
use Illuminate\Auth\Access\HandlesAuthorization;

// TODO: CRITICAL! Cover with tests.

class UserSshKeyPolicy
{
    use HandlesAuthorization;

    public function index(User $user, User $subject): bool
    {
        return $user->is($subject);
    }

    public function create(User $user, User $subject): bool
    {
        return $user->is($subject);
    }

    public function delete(User $user, UserSshKey $key): bool
    {
        // TODO: CRITICAL! Don't forget to implement deletion and update this check.
        return false;
    }
}
