<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /** Determine if a user can enable 2FA for another user. */
    public function enableTfa(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /** Determine if a user can disable 2FA for another user. */
    public function disableTfa(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->tfaEnabled();
    }

    /** Determine if a user can log out other sessions for another user. */
    public function logoutOtherSessions(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /** Determine if a user can change email address for another user. */
    public function changeEmail(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /** Determine if a user can change another user's password. */
    public function changePassword(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /** Determine if a user can delete another user's account. */
    public function delete(User $user, User $subject): bool
    {
        return $user->is($subject);
    }
}
