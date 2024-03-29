<?php declare(strict_types=1);

namespace App\Jobs\Servers;

use App\Exceptions\SshAuthFailedException;
use App\Jobs\AbstractRemoteServerJob;
use App\Notifications\Servers\ServerNotAvailableNotification;
use App\Scripts\Root\VerifyServerAvailabilityScript;
use App\States\Servers\Configuring;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class UpdateServerAvailabilityJob extends AbstractRemoteServerJob
{
    // Override normal job parameters to speed up the process.
    public int $timeout = 60; // 1 min
    public int|null $backoff = 10; // 10 sec
    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(5);
    }

    public function handle(VerifyServerAvailabilityScript $verifyServerAvailability): void
    {
        DB::transaction(function () use ($verifyServerAvailability) {
            $server = $this->server->freshLockForUpdate();

            // We will remove the availability status of the server before starting the checking process
            // in case it wasn't done immediately, so we could show the progress to user.
            if (! is_null($server->available)) {
                $server->available = null;
                $server->save();
                // This allows us to run the job doing only one transaction.
                $this->release();
                return;
            }

            try {
                $ssh = $server->sftp();
            } catch (SshAuthFailedException) {
                $server->available = false;
                $server->save();
                return;
            }

            if (! $ssh->isConnected()) {
                $this->release($this->backoff);
                return;
            }

            $server->available = $verifyServerAvailability->execute($server, $ssh);
            $server->save();

            if (! $server->available) {
                $this->notify();
                return;
            }

            $server->state->transitionTo(Configuring::class);
        });
    }

    public function failed(): void
    {
        $this->server->available = false;
        $this->server->save();

        $this->notify();
    }

    /** Notify the user that the server is not available. */
    protected function notify(): void
    {
        $this->server->user->notify(new ServerNotAvailableNotification($this->server));
    }
}
