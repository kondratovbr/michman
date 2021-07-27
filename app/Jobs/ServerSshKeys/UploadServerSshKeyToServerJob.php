<?php declare(strict_types=1);

namespace App\Jobs\ServerSshKeys;

use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Scripts\Root\UploadServerSshKeyScript;
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
    public function handle(UploadServerSshKeyScript $uploadServerSshKey): void
    {
        DB::transaction(function () use ($uploadServerSshKey) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $uploadServerSshKey->execute(
                $server,
                $server->serverSshKey,
                $this->username,
            );
        }, 5);
    }
}
