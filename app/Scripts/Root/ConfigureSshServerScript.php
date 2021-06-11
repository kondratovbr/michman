<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class ConfigureSshServerScript extends AbstractServerScript
{
    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $this->sendFile(
            '/etc/ssh/sshd_config',
            base_path('servers/ssh/sshd_config'),
        );
    }
}
