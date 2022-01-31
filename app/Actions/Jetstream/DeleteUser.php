<?php declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /** The team deleter implementation. */
    protected DeletesTeams $deletesTeams;

    public function __construct(DeletesTeams $deletesTeams)
    {
        $this->deletesTeams = $deletesTeams;
    }

    /**
     * Delete the given user.
     *
     * @param User $user
     */
    public function delete($user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteTeams($user);
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();

            // $user->subscription()->cancel();

            $user->delete();
        });
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
