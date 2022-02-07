<?php declare(strict_types=1);

namespace App\Jobs\Users;

use App\Jobs\AbstractJob;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// TODO: CRITICAL! CONTINUE.
//       Don't forget to logout from all the third-party providers.

// TODO: IMPORTANT! Cover with tests.

class DeleteUserJob extends AbstractJob
{
    /** The number of seconds the job can run before timing out. */
    public int $timeout = 60 * 5; // 5 min

    /** The number of seconds to wait before retrying the job. */
    public int $backoff = 60; // 1 min

    protected User $user;

    public function __construct(User $user)
    {
        parent::__construct();

        $this->user = $user->withoutRelations();
    }

    protected function getQueue(): string
    {
        return 'default';
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $user = $this->user->freshLockForUpdate();

            $this->deleteTokens($user);

            $this->deleteTeams($user);

            $this->deleteSshKeys($user);

            $this->revokeOAuthAuthorizations($user);

            $user->subscription()->cancel();

            $user->forceDelete();
        }, 5);
    }

    protected function deleteSshKeys(User $user): void
    {
        //
    }

    protected function revokeOAuthAuthorizations(User $user): void
    {
        // TODO: CRITICAL! CONTINUE. Create a custom OAuth facade to wrap Socialite and add revocation function. Make a custom driver Interface and wrap built-ins to add functionality.

        //
    }

    /** Delete the teams and team associations attached to the user. */
    protected function deleteTeams(User $user): void
    {
        $user->teams()->detach();

        $user->ownedTeams->each(fn(Team $team) => $team->purge());
    }

    protected function deleteTokens(User $user): void
    {
        $user->tokens->each->delete();
    }
}
