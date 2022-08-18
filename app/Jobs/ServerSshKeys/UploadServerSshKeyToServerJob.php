<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractRemoteServerJob;
use App\Models\Server;
use App\Notifications\Servers\FailedToUploadServerSshKeyToServerNotification;
use App\Scripts\Root\UploadSshKeyToServerScript;
use Illuminate\Support\Facades\DB;

class UploadServerSshKeyToServerJob extends AbstractRemoteServerJob
{
    protected string $username;

    public function __construct(Server $server, string $username)
    {
        parent::__construct($server);

        $this->username = $username;
    }

    public function handle(UploadSshKeyToServerScript $uploadSshKey): void
    {
        DB::transaction(function () use ($uploadSshKey) {
            $server = $this->server->freshSharedLock();

            $uploadSshKey->execute(
                $server,
                $server->serverSshKey,
                $this->username,
            );
        });
    }

    public function failed(): void
    {
        $this->server->user->notify(
            new FailedToUploadServerSshKeyToServerNotification($this->server)
        );
    }
}
