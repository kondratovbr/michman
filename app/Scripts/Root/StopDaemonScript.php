<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class StopDaemonScript extends AbstractServerScript
{
    public function execute(Server $server, Daemon $daemon, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->exec("supervisorctl stop {$daemon->name}");

        $this->exec("rm -f {$daemon->configPath()}");

        $this->exec("supervisorctl update {$daemon->name}");
    }
}
