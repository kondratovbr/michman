<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_8;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class PatchPythonScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get upgrade -y python3.8');
        $this->read();

        // Verify that Python works.
        if (trim($this->exec('python3.8 -c \'print("foobar")\'')) != 'foobar')
            throw new ServerScriptException('Python 3.8 installation failed - Python not accessible.');

        return trim(explode(
            ' ',
            $this->exec('python3.8 --version'),
            2
        )[1]);
    }
}
