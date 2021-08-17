<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class RestartNginxScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->exec("systemctl restart nginx");
        if ($this->failed())
            throw new ServerScriptException('systemctl command to restart Nginx has failed.');

        // Wait a bit for Nginx to be started by systemd.
        $this->setTimeout(60);
        $this->exec('sleep 30');

        // Verify that Nginx has started.
        $output = $this->exec('systemctl status nginx');
        if (
            ! Str::contains(Str::lower($output), 'active (running)')
            || $this->failed()
        ) {
            throw new ServerScriptException('Nginx failed to start.');
        }
    }
}
