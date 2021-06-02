<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_8;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;
use RuntimeException;

class InstallPythonScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();

        // TODO: IMPORTANT! Is this everything server needs to run generic Python/Django applications?
        //       Google Django deployment on Ubuntu!
        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y build-essential libssl-dev libffi-dev python3-dev');
        $this->read();

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get install -y python3.8 python3-pip python3-venv python3-virtualenv');
        $this->read();

        // Verify that Python works.
        if (trim($this->exec('python3.8 -c \'print("foobar")\'')) != 'foobar')
            throw new RuntimeException('Python 3.8 installation failed - Python not accessible.');
    }
}
