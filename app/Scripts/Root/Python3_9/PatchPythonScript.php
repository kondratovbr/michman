<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_9;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class PatchPythonScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get upgrade -y python3.9');
        $this->read();

        // Verify that Python works.
        if (trim($this->exec('python3.9 -c \'print("foobar")\'')) != 'foobar')
            throw new RuntimeException('Python 3.9 installation failed - Python not accessible.');

        return trim(explode(
            ' ',
            $this->exec('python3.9 --version'),
            2
        )[1]);
    }
}
