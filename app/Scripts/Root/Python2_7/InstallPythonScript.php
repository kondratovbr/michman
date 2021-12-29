<?php declare(strict_types=1);

namespace App\Scripts\Root\Python2_7;

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

        $this->execPty('apt-get install -y build-essential libssl-dev libffi-dev python2-dev');
        $this->read();

        $this->execPty('apt-get install -y python2.7 python3-pip python2-venv python2-virtualenv');
        $this->read();

        // Verify that Python works.
        if (! Str::contains($this->exec('python2.7 -c \'print("foobar")\''), 'foobar'))
            throw new ServerScriptException('Python 2.7 installation failed - Python not accessible.');

        $this->execPty('pip2.7 install --upgrade pip');
        $this->read();

        return trim(explode(
            ' ',
            $this->exec('python2.7 --version'),
            2
        )[1]);
    }
}
