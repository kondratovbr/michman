<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\Worker;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class RetrieveWorkerLogScript extends AbstractServerScript
{
    public function execute(Server $server, Worker $worker, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        return $this->exec("supervisorctl tail {$worker->name}");
    }
}
