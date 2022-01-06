<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithSystemd;
use phpseclib3\Net\SFTP;

class RestartNginxScript extends AbstractServerScript
{
    use InteractsWithSystemd;

    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->systemdRestartService('nginx', true, 60);
    }
}
