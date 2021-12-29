<?php declare(strict_types=1);

namespace App\Scripts\Root\Python3_8;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class DeletePythonScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('apt-get remove -y --auto-remove python3.8');
        $this->read();

        if ($this->failed())
            throw new ServerScriptException('apt-get remove has failed.');
    }
}
