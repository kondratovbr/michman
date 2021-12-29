<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_9;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithPython;
use phpseclib3\Net\SFTP;

class PatchPythonScript extends AbstractServerScript
{
    use InteractsWithPython;

    public function execute(Server $server, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('apt-get update -y');
        $this->read();

        $this->execPty('apt-get upgrade -y python3.9');
        $this->read();

        $this->verifyPythonWorks('3.9');

        return $this->getPythonVersion('3.9');
    }
}
