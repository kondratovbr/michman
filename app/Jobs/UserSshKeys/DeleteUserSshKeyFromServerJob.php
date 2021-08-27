<?php declare(strict_types=1);

namespace App\Jobs\UserSshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Project;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Scripts\Root\DeleteSshKeyFromUserScript;
use Illuminate\Support\Facades\DB;

class DeleteUserSshKeyFromServerJob extends AbstractRemoteServerJob
{
    protected UserSshKey $key;

    public function __construct(UserSshKey $key, Server $server)
    {
        parent::__construct($server);

        $this->key = $key->withoutRelations();
    }

    public function handle(DeleteSshKeyFromUserScript $script): void
    {
        DB::transaction(function () use ($script) {
            $server = $this->lockServer();
            $key = $this->key->freshLockForUpdate();

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
}
