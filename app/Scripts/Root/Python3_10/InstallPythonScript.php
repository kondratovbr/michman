<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_10;

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
            'python3-dev',
        ]);

        $this->aptInstall([
            "'^python3.10$'",
            'python3-pip',
            'python3-venv',
            'python3-virtualenv',
        ]);

        $this->verifyPythonWorks('3.10');

        $this->execPty('pip3.10 install --upgrade pip');
        $this->read();

        return $this->getPythonVersion('3.10');
    }
}
