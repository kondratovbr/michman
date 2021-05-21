<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RebootServerScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp('root'));

        $this->exec('reboot');
    }
}
