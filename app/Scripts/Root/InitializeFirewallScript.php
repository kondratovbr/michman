<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class InitializeFirewallScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        foreach ([
            'ufw disable',
            'ufw logging on',
            'ufw default deny routed',
            'ufw default deny incoming',
            'ufw default allow outgoing',
        ] as $command) {
            $this->exec($command);
        }
    }
}
