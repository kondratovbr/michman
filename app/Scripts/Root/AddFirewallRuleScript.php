<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class AddFirewallRuleScript extends AbstractServerScript
{
    public function execute(Server $server, string $port, bool $limit = false, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $type = $limit ? 'limit' : 'allow';

        $this->exec("ufw {$type} in {$port}/tcp");

        // This is to log the output into out server_logs table just in case.
        $this->exec('ufw status verbose');
    }
}
