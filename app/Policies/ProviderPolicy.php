<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProviderPolicy
{
    use HandlesAuthorization;

    /** Determine whether the user is allowed to see the list of providers of another user. */
    public function indexUser(User $user, User $owner): bool
    {
        // Users only allowed to see their own providers for now.
        return $user->is($owner);
    }

    public function create(User $user): bool
    {
        return true;
    }
}
