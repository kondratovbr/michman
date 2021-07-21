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

    public function __construct(Server $server)
    {
        $this->setQueue('servers');

        $this->server = $server->withoutRelations();
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
                (string) config('servers.worker_user'),
            );
        }, 5);
    }
}
