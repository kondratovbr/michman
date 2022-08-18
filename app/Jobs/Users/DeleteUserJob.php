<?php declare(strict_types=1);

namespace App\Jobs\Users;

use App\Jobs\AbstractJob;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

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

    /** Determine the time at which the job should timeout. */
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(30);
    }

    protected function getQueue(): string
    {
        return 'default';
    }

    public function handle(): void
    {
        DB::transaction(function () {
            /** @var User $user */
            $user = User::query()
                ->whereKey($this->user->getKey())
                ->isDeleting()
                ->lockForUpdate()
                ->firstOrFail();

            $this->deleteTokens($user);

            $this->deleteTeams($user);

            // Does nothing right now.
            // $this->revokeOAuthAuthorizations($user);

            $user->subscription()?->cancel();

            $user->purge();
        });
    }

    protected function revokeOAuthAuthorizations(User $user): void
    {
        /*
         * TODO: IMPORTANT! Do we actually need to do this? I found such functions in GitHub's and GitLab's APIs,
         *       but not in Bitbucket's. Will have to dig deeper.
         * https://docs.gitlab.com/ee/api/oauth2.html#revoke-a-token
         * https://docs.github.com/en/rest/reference/apps#delete-an-app-authorization
         */

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
        $user->tokens->each->purge();
    }
}
