<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\Worker;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class StopWorkerScript extends AbstractServerScript
{
    public function execute(Server $server, Worker $worker, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->setTimeout($worker->stopSeconds + 10); // 10s on top for good measure.

        $this->exec("supervisorctl stop {$worker->name}");

        $this->setTimeout();

        $this->exec("rm -f {$worker->configPath()}");

        $this->exec("supervisorctl update {$worker->name}");
    }
}
