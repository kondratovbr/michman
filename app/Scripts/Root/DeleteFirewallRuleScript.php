<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithUfw;
use phpseclib3\Net\SFTP;
use RuntimeException;

class DeleteFirewallRuleScript extends AbstractServerScript
{
    use InteractsWithUfw;

    public function execute(
        Server $server,
        string $port,
        bool $limit = false,
        string|null $fromIp = null,
        SFTP $ssh = null,
    ): void {
        $this->init($server, $ssh);

        $command = 'ufw delete ' . $this->ufwRule($port, $limit ? 'limit' : 'allow', $fromIp);

        $this->exec($command);

        if ($this->getExitStatus() !== 0)
            throw new RuntimeException("Failed to add a firewall rule. Command: \"{$command}\"");

        // This is to log the output into out server_logs table just in case.
        $this->exec('ufw status verbose');
    }
}
