<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class UpgradePackagesScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $this->execPty('apt-get update -y');
        $this->read();

        if ($this->failed())
            throw new ServerScriptException('apt-get update has failed.');

        $this->execPty('apt-get upgrade --with-new-pkgs -y');
        $this->read();

        if ($this->failed())
            throw new ServerScriptException('apt-get upgrade has failed.');

        $this->disablePty();
    }
}
