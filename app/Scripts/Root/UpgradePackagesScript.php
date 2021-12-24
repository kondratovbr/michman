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

        $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get update -y');
        $this->read();

        if ($this->failed())
            throw new ServerScriptException('apt-get update has failed.');

        /*
         * TODO: CRITICAL! Make sure this works without "noninteractive"
         *       and update the rest of the similar apt-get commands if it does.
         *       Intended to fix the "Could not get lock..." issue.
         */
        // $this->execPty('DEBIAN_FRONTEND=noninteractive apt-get upgrade --with-new-pkgs -y');
        $this->execPty('apt-get upgrade --with-new-pkgs -y');
        $this->read();

        if ($this->failed())
            throw new ServerScriptException('apt-get upgrade has failed.');

        $this->disablePty();
    }
}
