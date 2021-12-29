<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_8;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class InstallPythonScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        /*
         * The PTY and timeout are here to kill the script if apt-get gets stuck at some point -
         * it's a long-running thing and may get stuck due to external factors.
         */

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('apt-get update -y');
        $this->read();

        $this->execPty('apt-get install -y build-essential libssl-dev libffi-dev python3-dev');
        $this->read();

        $this->execPty('apt-get install -y python3.8 python3-pip python3-venv python3-virtualenv');
        $this->read();

        // Verify that Python works.
        if (! Str::contains($this->exec('python3.8 -c \'print("foobar")\''), 'foobar'))
            throw new ServerScriptException('Python 3.8 installation failed - Python not accessible.');

        $this->execPty('pip3.8 install --upgrade pip');
        $this->read();

        return trim(explode(
            ' ',
            $this->exec('python3.8 --version'),
            2
        )[1]);
    }
}
