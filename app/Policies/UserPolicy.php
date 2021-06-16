<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if a user can enable 2FA for a user.
     */
    public function enableTfa(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /**
     * Determine if a user can disable 2FA for a user.
     */
    public function disableTfa(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->tfaEnabled();
    }

    /**
     * Determine if a user can logout other sessions for a user.
     */
    public function logoutOtherSessions(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /**
     * Determine if a user can change email for a user.
     */
    public function changeEmail(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /**
     * Determine if a user can change a user's password.
     */
    public function changePassword(User $user, User $subject): bool
    {
        return $user->is($subject) && $user->usesPassword();
    }

    /**
     * Determine if a user can delete a user account.
     */
    public function deleteAccount(User $user, User $subject): bool
    {
        return $user->is($subject);
    }
}
