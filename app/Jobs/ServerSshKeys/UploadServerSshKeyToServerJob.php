<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Scripts\Root\UploadSshKeyToServerScript;
use Illuminate\Support\Facades\DB;

class UploadServerSshKeyToServerJob extends AbstractJob
{
    use InteractsWithRemoteServers;

    protected Server $server;
    protected string $username;

    public function __construct(Server $server, string $username)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
        $this->username = $username;
    }

    /**
     * Execute the job.
     */
    public function handle(UploadSshKeyToServerScript $uploadSshKey): void
    {
        DB::transaction(function () use ($uploadSshKey) {
            /** @var Server $server */
            $server = Server::query()->lockForUpdate()->findOrFail($this->server->getKey());

            $uploadSshKey->execute(
                $server,
                $server->serverSshKey,
                $this->username,
            );
        }, 5);
    }
}
