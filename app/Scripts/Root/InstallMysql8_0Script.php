<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class InstallMysql8_0Script extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        // TODO: CRITICAL! Continue!

        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server-8.0 mysql-client-8.0');
        $this->read();
        $this->exec('systemctl start mysql');
        $this->disablePty();
    }
}
