<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class UpgradePackagesScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp('root'));

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get upgrade --with-new-pkgs -y');
        $this->read();
        $this->disablePty();
    }
}
