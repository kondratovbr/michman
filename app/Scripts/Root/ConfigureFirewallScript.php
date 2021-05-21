<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class ConfigureFirewallScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp('root'));

        $this->setTimeout(60 * 5); // 5 min
        foreach ([
            'ufw disable',
            'ufw logging on',
            'ufw default deny routed',
            'ufw default deny incoming',
            'ufw default allow outgoing',
            "ufw limit in {$server->sshPort}/tcp",
            'ufw --force enable',
            'ufw status verbose',
        ] as $command) {
            $this->exec($command);
        }
    }
}
