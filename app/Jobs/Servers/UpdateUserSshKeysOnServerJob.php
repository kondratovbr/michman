<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Scripts\Root\AddSshKeyToUserScript;
use App\Scripts\Root\DisableSshAccessForUserScript;
use Illuminate\Support\Facades\DB;

class UpdateUserSshKeysOnServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(
        DisableSshAccessForUserScript $disableSshAccessForUser,
        AddSshKeyToUserScript $addSshKeyToUser,
    ): void {
        DB::transaction(function () use (
            $disableSshAccessForUser,
            $addSshKeyToUser,
        ) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $ssh = $server->sftp();

            // Remove all authorized SSH keys from the user.
            $disableSshAccessForUser->execute(
                $server,
                (string) config('servers.worker_user'),
                $ssh,
            );

            // Re-add our worker SSH key to the user.
            $addSshKeyToUser->execute(
                $server,
                (string) config('servers.worker_user'),
                $server->workerSshKey,
                $ssh,
            );

            // Add all user-added SSH keys as well.
            /** @var UserSshKey $userSshKey */
            foreach ($server->userSshKeys as $userSshKey) {
                $addSshKeyToUser->execute(
                    $server,
                    (string) config('servers.worker_user'),
                    $userSshKey,
                    $ssh,
                );
            }
        }, 5);
    }
}
