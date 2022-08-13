<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;

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

        $this->exec("systemctl stop $service");
    }

    protected function systemdDisableService(string $service): void
    {
        $service = Str::lower($service);

        $this->exec("systemctl disable $service");
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

        // Wait a bit in case the service is still starting up.
        $this->setTimeout($wait + 5);
        $this->exec("sleep $wait");

        if (! $this->systemdIsServiceRunning($service))
            throw new ServerScriptException("Systemd service \"$service\" has failed.");
    }
}
