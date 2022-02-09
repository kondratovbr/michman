<?php declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    public function __construct(
        /** The team deleter implementation. */
        protected DeletesTeams $deletesTeams,
    ) {}

    /**
     * Delete the given user.
     *
     * @param User $user
     */
    public function delete($user): void
    {
        throw new \RuntimeException('This action is deprecated and should not be used.');

        /*
        DB::transaction(function () use ($user) {
            $user->freshLockForUpdate();

            $this->deleteTeams($user);
            $user->tokens->each->delete();

            $user->subscription()->cancel();

            $user->delete();
        });
        */
    }

    /** Delete the teams and team associations attached to the user. */
    protected function deleteTeams($user): void
    {
        $user->teams()->detach();

        $user->ownedTeams->each(function ($team) {
            $this->deletesTeams->delete($team);
        });
    }
}
