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
            'python2-pip',
            'python2-venv',
            'python2-virtualenv',
        ]);

        $this->verifyPythonWorks('2.7');

        $this->execPty('pip2.7 install --upgrade pip');
        $this->read();

        return $this->getPythonVersion('2.7');
    }
}
