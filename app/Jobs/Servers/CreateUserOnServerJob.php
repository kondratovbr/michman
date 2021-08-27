<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Scripts\Root\AddSshKeyToUserScript;
use App\Scripts\Root\CreateGenericUserScript;
use Illuminate\Support\Facades\DB;

class CreateUserOnServerJob extends AbstractRemoteServerJob
{
    protected string $username;

    public function __construct(Server $server, string $username)
    {
        parent::__construct($server);

        $this->username = $username;
    }

    /**
     * Execute the job.
     */
    public function handle(
        CreateGenericUserScript $createUser,
        AddSshKeyToUserScript $addShhKey,
    ): void {
        DB::transaction(function () use ($createUser, $addShhKey) {
            /** @var Server $server */
            $server = Server::query()->lockForUpdate()->findOrFail($this->server->getKey());

            $ssh = $server->sftp();

            $createUser->execute(
                $server,
                $this->username,
                $ssh,
            );

            $addShhKey->execute(
                $server,
                $this->username,
                $server->workerSshKey,
                $ssh,
            );

            /** @var UserSshKey $userSshKey */
            foreach ($server->userSshKeys as $userSshKey) {
                $addShhKey->execute(
                    $server,
                    $this->username,
                    $userSshKey,
                    $ssh,
                );
            }
        });
    }
}
