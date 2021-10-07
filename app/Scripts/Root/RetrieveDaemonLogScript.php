<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Daemon;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RetrieveDaemonLogScript extends AbstractServerScript
{
    public function execute(Server $server, Daemon $daemon, SFTP $rootSsh = null): string
    {
        $this->init($server, $rootSsh);

        return $this->exec("supervisorctl tail {$daemon->name}");
    }
}
