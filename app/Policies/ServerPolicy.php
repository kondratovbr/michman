<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether a user is allowed to create a new server.
     */
    public function create(User $user): bool
    {
        // For now - no limits. Later will add subscription plan based limits here.
        return true;
    }
}
