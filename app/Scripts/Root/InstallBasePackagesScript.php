<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithApt;
use phpseclib3\Net\SFTP;

class InstallBasePackagesScript extends AbstractServerScript
{
    use InteractsWithApt;

    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh);

        $this->aptUpdate();

        $this->aptInstall([
            'ufw',
            'git',
            'curl',
            'gnupg',
            'gzip',
            'supervisor',
            'unattended-upgrades',
        ]);
    }
}
