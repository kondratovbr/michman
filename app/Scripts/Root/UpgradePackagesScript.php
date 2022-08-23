<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithApt;
use App\Support\Str;
use phpseclib3\Net\SFTP;

class UpgradePackagesScript extends AbstractServerScript
{
    use InteractsWithApt;

    public function execute(Server $server, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $this->aptPrepare();

        $this->aptUpdate();

        $output = $this->aptUpgrade();

        if (Str::contains($output, 'dpkg was interrupted')) {
            $this->execLong('DEBIAN_FRONTEND=noninteractive dpkg --configure -a', 60 * 30);
            throw new ServerScriptException('E: dpkg was interrupted, had to repair.');
        }
    }
}
