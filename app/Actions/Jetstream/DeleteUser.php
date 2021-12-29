<?php declare(strict_types=1);

namespace App\Actions\Jetstream;

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

    /** Delete the given user. */
    public function delete($user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteTeams($user);
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
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
