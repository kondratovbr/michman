<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Trait InteractsWithSystemd for server scripts.
 *
 * @mixin AbstractServerScript
 */
trait InteractsWithSystemd
{
    protected function systemdReload(): void
    {
        $this->exec('systemctl daemon-reload');
        $this->exec('systemctl reset-failed');
    }

    protected function systemdStartService(string $service): void
    {
        $service = Str::lower($service);

        $this->exec("systemctl start $service");
    }

    protected function systemdStopService(string $service): void
    {
        $service = Str::lower($service);

        $output = $this->exec("systemctl stop $service", throw: false);

        if ($output === false)
            $output = '[false]';

        if ($this->getExitStatus() === 5) {
            Log::info("Tried to stop non-existing systemd service: $service. Output: $output");
            return;
        }

        if ($this->failed())
            throw new ServerScriptException("Failed to stop systemd service: $service");
    }

    protected function systemdDisableService(string $service): void
    {
        $service = Str::lower($service);

        $output = $this->exec("systemctl disable $service", throw: false);

        if ($output === false)
            $output = '[false]';

        if ($this->getExitStatus() === 1) {
            Log::info("Tried to disable non-existing systemd service or possibly another issue: $service. Output: $output");
            return;
        }

        if ($this->failed())
            throw new ServerScriptException("Failed to disable systemd service: $service");
    }

    /**
     * Restart a systemd service with an option to verify that is started
     * and throw an exception otherwise.
     */
    protected function systemdRestartService(string $service, bool $verify = true, int $wait = 60): void
    {
        $service = Str::lower($service);

        $this->exec("systemctl restart $service");

        if (! $verify)
            return;

        // Wait a bit for the service to be started by systemd.
        /*
         * We're doing it this way because if we just say
         * sleep(60) the server receives no packets over SSH at all
         * during this time and may cut connection prematurely.
         */
        $this->setTimeout($wait + 5);
        $this->exec("sleep $wait");

        $this->systemdVerifyServiceIsRunning($service);
    }

    /** Use systemctl to verify that a service is running. */
    protected function systemdIsServiceRunning(string $service): bool
    {
        $service = Str::lower($service);

        $output = $this->exec("systemctl status $service");

        if ($this->failed())
            return false;

        return Str::contains(Str::lower($output), ['active (running)', 'active (listening)']);
    }

    /**
     * Check if a service is running and throw an exception is it doesn't.
     *
     * @param int $wait Wait for a service to start for this number of seconds.
     */
    protected function systemdVerifyServiceIsRunning(string $service, int|null $wait = null): void
    {
        $service = Str::lower($service);

        if ($wait) {
            // Wait a bit in case the service is still starting up.
            $this->setTimeout($wait + 5);
            $this->exec("sleep $wait");
        }

        if (! $this->systemdIsServiceRunning($service))
            throw new ServerScriptException("Systemd service \"$service\" has failed.");
    }
}
