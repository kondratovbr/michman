<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class UpdateSnapScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('DEBIAN_FRONTEND=noninteractive snap install core && snap refresh core');
        $this->read();

        $this->disablePty();
    }
}
