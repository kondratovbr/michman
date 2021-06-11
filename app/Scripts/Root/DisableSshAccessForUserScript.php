<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class DisableSshAccessForUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $directory = $username === 'root'
            ? '/root/.ssh'
            : "/home/{$username}/.ssh";

        $this->exec("rm -rf {$directory}");
    }
}
