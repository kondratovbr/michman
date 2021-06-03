<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class CreateSystemUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->exec('useradd -r -s /usr/sbin/nologin ' . $username);
    }
}
