<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class ConfigureUnattendedUpgradesScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        /*
         * TODO: I need to somehow verify that apt is actually using this config files
         *       and that unattended-upgrades actually work
         */

        $this->sendFile(
            '/etc/apt/apt.conf.d/20auto-upgrades',
            base_path('servers/apt/20auto-upgrades'),
        );
        $this->sendFile(
            '/etc/apt/apt.conf.d/50unattended-upgrades',
            base_path('servers/apt/50unattended-upgrades'),
        );
    }
}
