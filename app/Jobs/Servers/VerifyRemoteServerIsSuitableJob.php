<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Jobs\AbstractRemoteServerJob;
use App\Notifications\Servers\ServerIsNotSuitableNotification;
use App\Scripts\Root\VerifyServerIsSuitableScript;
use App\States\Servers\Preparing;
use Illuminate\Support\Facades\DB;

// TODO: IMPORTANT! Cover with tests.

class VerifyRemoteServerIsSuitableJob extends AbstractRemoteServerJob
{
    // Override the normal backoff time to speed up the server creation process for users.
    public int $backoff = 10; // 10 sec

    public function handle(VerifyServerIsSuitableScript $verifyServerIsSuitable): void
    {
        DB::transaction(function () use ($verifyServerIsSuitable) {
            $server = $this->server->freshLockForUpdate();

            try {
                $ssh = $server->sftp('root');
            } catch (SshAuthFailedException) {
                $server->suitable = false;
                $server->save();

                $this->notify();

                return;
            }

            if (! $ssh->isConnected()) {
                $this->release($this->backoff);
                return;
            }

            $server->suitable = $verifyServerIsSuitable->execute($server, $ssh);
            $server->save();

            if (! $server->suitable) {
                $this->notify();
                return;
            }

            $server->state->transitionTo(Preparing::class);
        });
    }

    /** Notify the user that the server isn't suitable. */
    protected function notify(): void
    {
        $this->server->user->notify(new ServerIsNotSuitableNotification($this->server));
    }

    public function failed(): void
    {
        $this->notify();
    }
}
