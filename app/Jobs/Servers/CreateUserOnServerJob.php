<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Server;
use App\Models\UserSshKey;
use App\Notifications\Servers\FailedToCreateNewUserOnServerNotification;
use App\Scripts\Root\AddSshKeyToUserScript;
use App\Scripts\Root\CreateGenericUserScript;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateUserOnServerJob extends AbstractRemoteServerJob
{
    protected string $username;

    public function __construct(Server $server, string $username)
    {
        parent::__construct($server);

        $this->username = $username;
    }

    public function handle(
        CreateGenericUserScript $createUser,
        AddSshKeyToUserScript $addShhKey,
    ): void {
        DB::transaction(function () use ($createUser, $addShhKey) {
            $server = $this->server->freshLockForUpdate();

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

    public function failed(Throwable $exception): void
    {
        $this->server->user->notify(new FailedToCreateNewUserOnServerNotification($this->server));
    }
}
