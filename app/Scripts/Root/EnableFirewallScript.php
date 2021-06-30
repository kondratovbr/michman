<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class EnableFirewallScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        foreach ([
             'ufw --force enable',
             // This is to log the output into out server_logs table just in case.
             'ufw status verbose',
         ] as $command) {
            $this->exec($command);
        }
    }
}
