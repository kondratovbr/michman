<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class CreateGenericUserScript extends AbstractServerScript
{
    public function execute(Server $server, string $username, SFTP $ssh = null)
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $this->setTimeout(60);

        $this->exec("useradd --create-home --shell /bin/bash $username");

        $homeDir = "/home/$username";
        $michmanDir = "$homeDir/.michman";

        $this->exec("mkdir -p $michmanDir && chown -R $username:$username $homeDir && chmod 0755 $homeDir && chmod 0755 $michmanDir");
    }
}
