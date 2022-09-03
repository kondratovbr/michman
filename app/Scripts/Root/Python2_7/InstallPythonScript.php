<?php declare(strict_types=1);

namespace App\Scripts\Root\Python2_7;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithApt;
use App\Scripts\Traits\InteractsWithPython;
use phpseclib3\Net\SFTP;

class InstallPythonScript extends AbstractServerScript
{
    use InteractsWithPython;
    use InteractsWithApt;

    public function execute(Server $server, SFTP $ssh = null): string
    {
        $this->init($server, $ssh);

        $this->aptUpdate();

        $this->aptInstall([
            'build-essential',
            'libssl-dev',
            'libffi-dev',
            'python2-dev',
        ]);

        $this->aptInstall([
            "'^python2.7$'",
            'python-pip',
        ]);

        // We will be using Python 3 for our own needs,
        // specifically venv.
        $this->aptInstall([
            "'^python3.8$'",
            'python3.8-dev',
            'python3.8-distutils',
            'libpython3.8-dev',
            'python3-pip',
            'python3-venv',
            'python3-virtualenv',
        ]);

        $this->verifyPythonWorks('2.7');

        $this->execPty('pip2 install --upgrade pip');
        $this->read();

        return $this->getPythonVersion('2.7');
    }
}
