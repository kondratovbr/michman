<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\VcsProvider;
use Illuminate\Auth\Access\HandlesAuthorization;

class VcsProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if a user can create a VcsProvider for himself.
     */
    public function create(User $user, string $provider): bool
    {
        return is_null($user->vcs($provider));
    }

    /**
     * Determine if a user can update a VcsProvider.
     */
    public function update(User $user, VcsProvider $vcsProvider): bool
    {
        return $user->is($vcsProvider->user);
    }

    /**
     * Determine if a user can delete a VcsProvider.
     */
    public function delete(User $user, VcsProvider $vcsProvider): bool
    {
        return false;
    }
}