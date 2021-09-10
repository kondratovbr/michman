<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Jobs\AbstractRemoteServerJob;
use App\Scripts\Root\VerifyServerIsSuitableScript;
use Illuminate\Support\Facades\DB;

class VerifyRemoteServerIsSuitableJob extends AbstractRemoteServerJob
{
    // Override the normal backoff time to speed up the server creation process for users.
    public int $backoff = 10; // 10 sec

    /**
     * Execute the job.
     */
    public function handle(VerifyServerIsSuitableScript $verifyServerIsSuitable): void
    {
        DB::transaction(function () use ($verifyServerIsSuitable) {
            $server = $this->server->freshLockForUpdate();

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
