<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Jobs\AbstractJob;
use App\Jobs\Traits\InteractsWithRemoteServers;
use App\Models\Server;
use App\Scripts\Root\VerifyServerIsSuitableScript;
use Illuminate\Support\Facades\DB;

class VerifyRemoteServerIsSuitableJob extends AbstractJob
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
    public function handle(VerifyServerIsSuitableScript $verifyServerIsSuitable): void
    {
        DB::transaction(function () use ($verifyServerIsSuitable) {
            /** @var Server $server */
            $server = Server::query()
                ->whereKey($this->server->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            try {
                $ssh = $server->sftp('root');
            } catch (SshAuthFailedException $exception) {
                $server->suitable = false;
                $server->save();
                return;
            }

            if (! $ssh->isConnected()) {
                $this->release($this->backoff);
                return;
            }

            $server->suitable = $verifyServerIsSuitable->execute($server, $ssh);
            $server->save();
        }, 5);
    }
}
