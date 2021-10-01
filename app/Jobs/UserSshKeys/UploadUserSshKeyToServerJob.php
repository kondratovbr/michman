<?php declare(strict_types=1);

namespace App\Jobs\UserSshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Notifications\Servers\FailedToAddSshKeyToServerNotification;
use App\Scripts\Root\AddSshKeyToUserScript;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UploadUserSshKeyToServerJob extends AbstractRemoteServerJob
{
    protected UserSshKey $key;

    public function __construct(UserSshKey $key, Server $server, bool $sync = false)
    {
        parent::__construct($server, $sync);

        $this->key = $key->withoutRelations();
    }

    public function handle(AddSshKeyToUserScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->server->freshSharedLock();
            $key = $this->key->freshLockForUpdate();

            if (! $server->userSshKeys->contains($key))
                $this->fail(new RuntimeException('This UserSshKey is not attached to this Server.'));

            $ssh = $server->sftp();

            $script->execute(
                $server,
                (string) config('servers.worker_user'),
                $key,
                $ssh,
            );

            /** @var Project $project */
            foreach ($server->projects as $project) {
                $script->execute(
                    $server,
                    $project->serverUsername,
                    $key,
                    $ssh,
                );
            }
        }, 5);
    }

    public function failed(): void
    {
        $this->server->user->notify(new FailedToAddSshKeyToServerNotification($this->server));
    }
}
